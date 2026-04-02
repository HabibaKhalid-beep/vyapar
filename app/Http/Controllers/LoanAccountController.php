<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\LoanAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanAccountController extends Controller
{
    public function index()
    {
        $loanAccounts = LoanAccount::with(['lenderBank', 'receivedInBank', 'processingFeeBank'])->orderByDesc('created_at')->get();
        $banks = BankAccount::orderBy('display_name')->get();

        return view('dashboard.accounts.loan', compact('loanAccounts', 'banks'));
    }

    public function show(LoanAccount $loanAccount)
    {
        // Return JSON for JS-powered edit form and other UI use.
        $loanAccount->load(['lenderBank', 'receivedInBank', 'processingFeeBank']);
        return response()->json($loanAccount);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'display_name' => 'required|string|max:255',
            'lender_bank_id' => 'nullable|exists:bank_accounts,id',
            'account_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'current_balance' => 'required|numeric',
            'balance_as_of' => 'nullable|date',
            'received_in' => 'required|exists:bank_accounts,id',
            'interest_rate' => 'nullable|numeric',
            'term_months' => 'nullable|integer',
            'processing_fee' => 'nullable|numeric',
            'processing_fee_paid_from_id' => 'nullable|exists:bank_accounts,id',
        ]);

        $data['processing_fee'] = $data['processing_fee'] ?? 0;

        DB::transaction(function () use ($data) {
            /** @var LoanAccount $loan */
            $loan = LoanAccount::create($data);

            // Deduct processing fee from the selected bank account.
            if ($data['processing_fee'] > 0 && !empty($data['processing_fee_paid_from_id'])) {
                $bank = BankAccount::lockForUpdate()->find($data['processing_fee_paid_from_id']);
                if ($bank) {
                    $bank->opening_balance -= $data['processing_fee'];
                    $bank->save();

                    BankTransaction::create([
                        'from_bank_account_id' => $bank->id,
                        'type' => 'loan_processing_fee',
                        'amount' => $data['processing_fee'],
                        'transaction_date' => $data['balance_as_of'] ?? now()->toDateString(),
                        'reference_type' => LoanAccount::class,
                        'reference_id' => $loan->id,
                        'description' => 'Loan processing fee deducted',
                        'meta' => [
                            'loan_name' => $loan->display_name,
                            'action' => 'deduct',
                        ],
                    ]);
                }
            }
        });

        return redirect()->route('loan-accounts')->with('success', 'Loan account added successfully.');
    }

    public function edit(LoanAccount $loanAccount)
    {
        // Return JSON for JS-powered edit form.
        return response()->json($loanAccount);
    }

    public function update(Request $request, LoanAccount $loanAccount)
    {
        $data = $request->validate([
            'display_name' => 'required|string|max:255',
            'lender_bank_id' => 'nullable|exists:bank_accounts,id',
            'account_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'current_balance' => 'required|numeric',
            'balance_as_of' => 'nullable|date',
            'received_in' => 'required|exists:bank_accounts,id',
            'interest_rate' => 'nullable|numeric',
            'term_months' => 'nullable|integer',
            'processing_fee' => 'nullable|numeric',
            'processing_fee_paid_from_id' => 'nullable|exists:bank_accounts,id',
        ]);

        $data['processing_fee'] = $data['processing_fee'] ?? 0;

        DB::transaction(function () use ($data, $loanAccount) {
            $oldFee = $loanAccount->processing_fee ?? 0;
            $oldFeeBankId = $loanAccount->processing_fee_paid_from_id;

            $loanAccount->update($data);

            $newFee = $data['processing_fee'];
            $newFeeBankId = $data['processing_fee_paid_from_id'] ?? null;

            // If fee bank changed, refund old fee to old bank first.
            if ($oldFeeBankId && $oldFeeBankId !== $newFeeBankId && $oldFee > 0) {
                $oldBank = BankAccount::lockForUpdate()->find($oldFeeBankId);
                if ($oldBank) {
                    $oldBank->opening_balance += $oldFee;
                    $oldBank->save();

                    BankTransaction::create([
                        'to_bank_account_id' => $oldBank->id,
                        'type' => 'loan_processing_fee_refund',
                        'amount' => $oldFee,
                        'transaction_date' => $data['balance_as_of'] ?? now()->toDateString(),
                        'reference_type' => LoanAccount::class,
                        'reference_id' => $loanAccount->id,
                        'description' => 'Loan processing fee refunded',
                        'meta' => [
                            'loan_name' => $loanAccount->display_name,
                            'action' => 'refund_old_fee',
                        ],
                    ]);
                }
            }

            // Adjust new fee bank by fee delta (handles same bank or new bank).
            if ($newFeeBankId && $newFee > 0) {
                $feeDelta = $newFee;
                if ($oldFeeBankId === $newFeeBankId) {
                    $feeDelta = $newFee - $oldFee;
                }
                if ($feeDelta !== 0) {
                    $bank = BankAccount::lockForUpdate()->find($newFeeBankId);
                    if ($bank) {
                        $bank->opening_balance -= $feeDelta;
                        $bank->save();

                        $transactionPayload = [
                            'type' => $feeDelta > 0 ? 'loan_processing_fee' : 'loan_processing_fee_refund',
                            'amount' => abs($feeDelta),
                            'transaction_date' => $data['balance_as_of'] ?? now()->toDateString(),
                            'reference_type' => LoanAccount::class,
                            'reference_id' => $loanAccount->id,
                            'description' => $feeDelta > 0 ? 'Loan processing fee deducted' : 'Loan processing fee adjustment reversal',
                            'meta' => [
                                'loan_name' => $loanAccount->display_name,
                                'action' => $feeDelta > 0 ? 'deduct_delta' : 'reverse_delta',
                            ],
                        ];

                        if ($feeDelta > 0) {
                            $transactionPayload['from_bank_account_id'] = $bank->id;
                        } else {
                            $transactionPayload['to_bank_account_id'] = $bank->id;
                        }

                        BankTransaction::create($transactionPayload);
                    }
                }
            }
        });

        return redirect()->route('loan-accounts')->with('success', 'Loan account updated successfully.');
    }

    public function destroy(LoanAccount $loanAccount)
    {
        DB::transaction(function () use ($loanAccount) {
            $fee = $loanAccount->processing_fee ?? 0;
            $feeBankId = $loanAccount->processing_fee_paid_from_id;

            $loanAccount->delete();

            if ($fee > 0 && $feeBankId) {
                $bank = BankAccount::lockForUpdate()->find($feeBankId);
                if ($bank) {
                    $bank->opening_balance += $fee;
                    $bank->save();

                    BankTransaction::create([
                        'to_bank_account_id' => $bank->id,
                        'type' => 'loan_processing_fee_refund',
                        'amount' => $fee,
                        'transaction_date' => now()->toDateString(),
                        'reference_type' => LoanAccount::class,
                        'reference_id' => $loanAccount->id,
                        'description' => 'Loan processing fee refunded on delete',
                        'meta' => [
                            'loan_name' => $loanAccount->display_name,
                            'action' => 'delete_refund',
                        ],
                    ]);
                }
            }
        });

        return redirect()->route('loan-accounts')->with('success', 'Loan account deleted successfully.');
    }
}
