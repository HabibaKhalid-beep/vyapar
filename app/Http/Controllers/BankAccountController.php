<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankAccountController extends Controller
{
    public function index()
    {
        $bankAccounts = BankAccount::orderByDesc('created_at')->get();
        $bankTransactions = SalePayment::with(['sale', 'bankAccount'])
            ->whereNotNull('bank_account_id')
            ->orderByDesc('created_at')
            ->get();

        return view('dashboard.accounts.bank', compact('bankAccounts', 'bankTransactions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'display_name' => 'required|string|max:255',
            'opening_balance' => 'nullable|numeric',
            'as_of_date' => 'nullable|date',
            'account_number' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'print_on_invoice' => 'nullable|boolean',
        ]);

        $data['print_on_invoice'] = $request->has('print_on_invoice');

        $bankAccount = BankAccount::create($data);

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Bank account added successfully.', 'bank' => $bankAccount]);
        }

        return redirect()->route('bank-accounts')->with('success', 'Bank account added successfully.');
    }

    public function show(BankAccount $bankAccount)
    {
        // Return JSON for viewing/editing via AJAX.
        if (request()->wantsJson()) {
            return response()->json($bankAccount);
        }

        // Fallback: redirect to listing if accessed via browser.
        return redirect()->route('bank-accounts');
    }

    public function edit(BankAccount $bankAccount)
    {
        // Return JSON for JS-powered edit form.
        return response()->json($bankAccount);
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $data = $request->validate([
            'display_name' => 'required|string|max:255',
            'opening_balance' => 'nullable|numeric',
            'as_of_date' => 'nullable|date',
            'account_number' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'print_on_invoice' => 'nullable|boolean',
        ]);

        $data['print_on_invoice'] = $request->has('print_on_invoice');

        $bankAccount->update($data);

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Bank account updated successfully.', 'bank' => $bankAccount]);
        }

        return redirect()->route('bank-accounts')->with('success', 'Bank account updated successfully.');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Bank account deleted successfully.']);
        }

        return redirect()->route('bank-accounts')->with('success', 'Bank account deleted successfully.');
    }

    public function transfer(Request $request)
    {
        $data = $request->validate([
            'mode' => 'required|in:bank_to_bank,bank_to_cash,cash_to_bank,adjust_balance',
            'from_bank_id' => 'nullable|exists:bank_accounts,id',
            'to_bank_id' => 'nullable|exists:bank_accounts,id|different:from_bank_id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($data['mode'] !== 'bank_to_bank') {
            return response()->json([
                'message' => 'Only bank to bank transfer is active right now.',
            ], 422);
        }

        $fromBank = BankAccount::findOrFail($data['from_bank_id']);
        $toBank = BankAccount::findOrFail($data['to_bank_id']);
        $amount = (float) $data['amount'];

        if ((float) ($fromBank->opening_balance ?? 0) < $amount) {
            return response()->json([
                'message' => 'Insufficient balance in source bank.',
            ], 422);
        }

        DB::transaction(function () use ($fromBank, $toBank, $amount) {
            $fromBank->opening_balance = (float) ($fromBank->opening_balance ?? 0) - $amount;
            $toBank->opening_balance = (float) ($toBank->opening_balance ?? 0) + $amount;

            $fromBank->save();
            $toBank->save();

            BankTransaction::create([
                'from_bank_account_id' => $fromBank->id,
                'to_bank_account_id' => $toBank->id,
                'type' => 'bank_to_bank',
                'amount' => $amount,
                'transaction_date' => now()->toDateString(),
                'description' => 'Bank to bank transfer',
                'meta' => [
                    'from_bank_name' => $fromBank->display_name,
                    'to_bank_name' => $toBank->display_name,
                ],
            ]);
        });

        return response()->json([
            'message' => 'Bank to bank transfer completed successfully.',
            'from_bank_balance' => $fromBank->fresh()->opening_balance,
            'to_bank_balance' => $toBank->fresh()->opening_balance,
        ]);
    }

    public function cashInHand()
    {
        // For simplicity, we can treat "Cash in Hand" as a special bank account with a fixed ID (e.g., 0).
        // Alternatively, you could have a separate model/table for cash transactions.


        return view('dashboard.accounts.cash-hand');
    }
}
