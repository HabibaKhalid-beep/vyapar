<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Party;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseExpenseController extends Controller
{
    //



    public function paymentOut()
    {
        $paymentOuts = PurchasePayment::with(['purchase.party', 'bankAccount'])
            ->latest()
            ->get();

        $transactionMap = Transaction::query()
            ->where('type', 'payment_out')
            ->whereIn('number', $paymentOuts->pluck('receipt_no')->filter()->values())
            ->get()
            ->keyBy('number');

        $bankAccounts = BankAccount::query()
            ->where('is_active', true)
            ->orderBy('display_name')
            ->get();

        $parties = Party::query()
            ->orderBy('name')
            ->get();

        $pendingPurchases = Purchase::with('party')
            ->whereIn('type', ['purchase', 'purchase_bill'])
            ->where('balance', '>', 0)
            ->orderByDesc('bill_date')
            ->get();

        $nextEntryNo = (Transaction::max('id') ?? 0) + 1;

        return view('dashboard.purchases.payement-out', compact('paymentOuts', 'bankAccounts', 'parties', 'pendingPurchases', 'transactionMap', 'nextEntryNo'));
    }

    public function storePaymentOut(Request $request)
    {
        $data = $request->validate([
            'purchase_id' => ['required', 'exists:purchases,id'],
            'payment_type' => ['required', 'string', 'max:50'],
            'bank_account_id' => ['nullable', 'exists:bank_accounts,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'reference' => ['nullable', 'string', 'max:255'],
            'receipt_no' => ['nullable', 'string', 'max:255'],
            'payment_date' => ['nullable', 'date'],
        ]);

        $purchase = Purchase::query()->findOrFail($data['purchase_id']);
        $amount = round((float) $data['amount'], 2);
        $currentBalance = round((float) ($purchase->balance ?? 0), 2);

        if ($currentBalance <= 0) {
            return redirect()->route('payment-out')->withErrors(['amount' => 'Selected purchase bill is already fully paid.']);
        }

        if ($amount - $currentBalance > 0.001) {
            return redirect()->route('payment-out')->withErrors(['amount' => 'Payment amount balance due se zyada nahi ho sakta.']);
        }

        $paymentDate = !empty($data['payment_date']) ? Carbon::parse($data['payment_date']) : now();

        DB::transaction(function () use ($data, $purchase, $amount, $paymentDate) {
            $payment = new PurchasePayment([
                'purchase_id' => $purchase->id,
                'payment_type' => strtolower((string) $data['payment_type']),
                'bank_account_id' => $data['bank_account_id'] ?? null,
                'amount' => $amount,
                'reference' => $data['reference'] ?? null,
                'receipt_no' => $data['receipt_no'] ?? null,
            ]);
            $payment->created_at = $paymentDate;
            $payment->updated_at = $paymentDate;
            $payment->save();

            Transaction::create([
                'party_id' => $purchase->party_id,
                'type' => 'payment_out',
                'number' => $data['receipt_no'] ?? null,
                'date' => $paymentDate->toDateString(),
                'total' => $amount,
                'credit' => $amount,
                'paid_amount' => $amount,
                'status' => 'paid',
                'description' => trim('Payment Out'
                    . (($data['reference'] ?? null) ? ' | Ref: ' . $data['reference'] : '')
                    . (($data['receipt_no'] ?? null) ? ' | Receipt: ' . $data['receipt_no'] : '')
                ),
            ]);

            $purchase->paid_amount = round((float) ($purchase->paid_amount ?? 0) + $amount, 2);
            $purchase->balance = round(max(0, (float) ($purchase->grand_total ?? 0) - (float) $purchase->paid_amount), 2);
            $purchase->save();
        });

        return redirect()->route('payment-out')->with('success', 'Payment out saved successfully.');
    }




    public function purchaseReturn()
    {
        return view('dashboard.purchases.purchase-return');
    }


}
