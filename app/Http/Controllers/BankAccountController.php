<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\PurchasePayment;
use App\Models\SalePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankAccountController extends Controller
{
    public function index()
    {
        $bankAccounts = BankAccount::orderByDesc('created_at')->get();

        $saleTransactions = SalePayment::with(['sale.party', 'bankAccount'])
            ->whereNotNull('bank_account_id')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($payment) {
                $typeLabel = match ($payment->sale?->type) {
                    'invoice' => 'Sale Invoice',
                    'estimate' => 'Estimate',
                    'sale_order' => 'Sale Order',
                    'proforma' => 'Proforma',
                    'delivery_challan' => 'Delivery Challan',
                    'sale_return' => 'Sale Return',
                    'pos' => 'POS',
                    default => ucfirst(str_replace('_', ' ', $payment->sale?->type ?? 'Unknown')),
                };

                return (object) [
                    'bank_account_id' => $payment->bank_account_id,
                    'type_label' => $typeLabel,
                    'invoice_no' => $payment->sale?->bill_number ?? '-',
                    'party_name' => $payment->sale?->display_party_name ?? '-',
                    'bank_name' => $payment->bankAccount?->display_name ?? $payment->bankAccount?->bank_name ?? '-',
                    'payment_type' => $payment->payment_type ?? '-',
                    'created_at' => $payment->created_at,
                    'amount' => (float) ($payment->amount ?? 0),
                ];
            });

        $purchaseTransactions = PurchasePayment::with(['purchase.party', 'bankAccount'])
            ->whereNotNull('bank_account_id')
            ->whereHas('purchase', fn ($query) => $query->whereIn('type', ['purchase_bill', 'purchase_return']))
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($payment) {
                $typeLabel = match ($payment->purchase?->type) {
                    'purchase_bill' => 'Purchase Bill',
                    'purchase_return' => 'Purchase Return',
                    'purchase_order' => 'Purchase Order',
                    default => ucfirst(str_replace('_', ' ', $payment->purchase?->type ?? 'Unknown')),
                };

                return (object) [
                    'bank_account_id' => $payment->bank_account_id,
                    'type_label' => $typeLabel,
                    'invoice_no' => $payment->purchase?->bill_number ?? '-',
                    'party_name' => $payment->purchase?->party?->name
                        ?? $payment->purchase?->party_name
                        ?? '-',
                    'bank_name' => $payment->bankAccount?->display_name ?? $payment->bankAccount?->bank_name ?? '-',
                    'payment_type' => $payment->payment_type ?? '-',
                    'created_at' => $payment->created_at,
                    'amount' => (float) ($payment->amount ?? 0),
                ];
            });

        $bankTransactions = $saleTransactions
            ->concat($purchaseTransactions)
            ->sortByDesc(fn ($transaction) => $transaction->created_at?->timestamp ?? 0)
            ->values();

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
  public function paymentIn(Request $request)
{
    $data = $request->validate([
        'party_id'                   => 'required|exists:parties,id',
        'payments'                   => 'required|array',
        'payments.*.type'            => 'required|string',
        'payments.*.amount'          => 'required|numeric|min:0',
        'payments.*.bank_account_id' => 'nullable|exists:bank_accounts,id',
        'reference_no'               => 'nullable|string',
        'receipt_no'                 => 'nullable|string',
        'date'                       => 'nullable|date',
    ]);

    $totalAmount = collect($data['payments'])->sum('amount');

    // ✅ 1. PaymentIn record save karo
    foreach ($data['payments'] as $payment) {
        \App\Models\PaymentIn::create([
            'party_id'        => $data['party_id'],
            'bank_account_id' => !empty($payment['bank_account_id']) ? $payment['bank_account_id'] : null,
            'amount'          => $payment['amount'],
            'payment_type'    => $payment['type'],
            'reference_no'    => $data['reference_no'] ?? null,
            'receipt_no'      => $data['receipt_no'] ?? null,
            'date'            => $data['date'] ?? now(),
        ]);
    }

    // ✅ 2. Party balance minus karo
    $party = \App\Models\Party::findOrFail($data['party_id']);
    $party->opening_balance = ($party->opening_balance ?? 0) - $totalAmount;
    $party->save();

    // ✅ 3. Bank balance add karo — bank_account_id check properly
    foreach ($data['payments'] as $payment) {
        $bankId = $payment['bank_account_id'] ?? null;

        if (!empty($bankId) && is_numeric($bankId)) { // ✅ Proper check
            $bank = BankAccount::find($bankId);
            if ($bank) {
                $bank->opening_balance = ($bank->opening_balance ?? 0) + floatval($payment['amount']);
                $bank->save();
            }
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Payment recorded successfully.'
    ]);
}
}
