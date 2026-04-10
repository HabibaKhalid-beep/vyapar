<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Party;
use App\Models\Transaction;
use App\Models\BankAccount;
use App\Models\PaymentIn;
use Illuminate\Support\Facades\DB;

class PaymentInController extends Controller
{
    public function index(Request $request)
    {
        return view('dashboard.sales.payement-in', [
            'parties'      => Party::all(),
            'bankAccounts' => BankAccount::active()->get(),
            'paymentIns'   => PaymentIn::with(['party', 'bankAccount'])->latest()->get(),
            'editPaymentIn' => $request->filled('edit_payment_in')
                ? PaymentIn::with(['party', 'bankAccount'])->find($request->integer('edit_payment_in'))
                : null,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'party_id'                   => 'required|exists:parties,id',
            'payments'                   => 'required|array|min:1',
            'payments.*.type'            => 'required|string',
            'payments.*.amount'          => 'required|numeric|min:1',
            'payments.*.bank_account_id' => 'nullable|exists:bank_accounts,id',
            'date'                       => 'required|date',
            'reference_no'               => 'nullable|string',
            'receipt_no'                 => 'nullable|string',
            'description'                => 'nullable|string',
        ]);

        try {
            $party = Party::findOrFail($request->party_id);
            $savedPayments = collect();

            DB::transaction(function () use ($request, $party, &$savedPayments) {
                foreach ($request->payments as $pay) {
                    $paymentIn = PaymentIn::create([
                        'party_id'        => $party->id,
                        'bank_account_id' => $pay['bank_account_id'] ?? null,
                        'amount'          => $pay['amount'],
                        'payment_type'    => $pay['type'],
                        'reference_no'    => $request->reference_no ?? null,
                        'receipt_no'      => $request->receipt_no ?? null,
                        'date'            => $request->date,
                        'description'     => $request->description ?? null,
                    ]);

                    $savedPayments->push($paymentIn);

                    Transaction::create([
                        'party_id'        => $party->id,
                        'type'            => 'receive',
                        'date'            => $request->date,
                        'total'           => $pay['amount'],
                        'paid_amount'     => $pay['amount'],
                        'debit'           => $pay['amount'],
                        'status'          => 'receive',
                        'description'     => trim('Payment In'
                            . (($request->reference_no ?? null) ? ' | Ref: ' . $request->reference_no : '')
                            . (($request->receipt_no ?? null) ? ' | Receipt: ' . $request->receipt_no : '')
                        ),
                    ]);

                    $party->opening_balance = (float) ($party->opening_balance ?? 0) - (float) $pay['amount'];
                    $party->save();

                    if (!empty($pay['bank_account_id'])) {
                        $bank = BankAccount::findOrFail($pay['bank_account_id']);
                        $bank->opening_balance = (float) ($bank->opening_balance ?? 0) + (float) $pay['amount'];
                        $bank->save();
                    }
                }
            });

            $latestPayment = $savedPayments->last();

            return response()->json([
                'success' => true,
                'message' => 'Payment record ho gaya!',
                'redirect_url' => $latestPayment
                    ? route('invoice', ['payment_in' => $latestPayment->id])
                    : route('invoice'),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(PaymentIn $paymentIn)
    {
        return redirect()->route('payment-in', ['edit_payment_in' => $paymentIn->id]);
    }

    public function update(Request $request, PaymentIn $paymentIn)
    {
        $request->validate([
            'party_id'                   => 'required|exists:parties,id',
            'payments'                   => 'required|array|min:1|max:1',
            'payments.*.type'            => 'required|string',
            'payments.*.amount'          => 'required|numeric|min:1',
            'payments.*.bank_account_id' => 'nullable|exists:bank_accounts,id',
            'date'                       => 'required|date',
            'reference_no'               => 'nullable|string',
            'receipt_no'                 => 'nullable|string',
            'description'                => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $paymentIn) {
                $oldAmount = (float) ($paymentIn->amount ?? 0);
                $newPayment = $request->payments[0];
                $newAmount = (float) ($newPayment['amount'] ?? 0);

                $oldParty = Party::find($paymentIn->party_id);
                if ($oldParty) {
                    $oldParty->opening_balance = (float) ($oldParty->opening_balance ?? 0) + $oldAmount;
                    $oldParty->save();
                }

                if ($paymentIn->bank_account_id) {
                    $oldBank = BankAccount::find($paymentIn->bank_account_id);
                    if ($oldBank) {
                        $oldBank->opening_balance = (float) ($oldBank->opening_balance ?? 0) - $oldAmount;
                        $oldBank->save();
                    }
                }

                $paymentIn->update([
                    'party_id'        => $request->party_id,
                    'bank_account_id' => $newPayment['bank_account_id'] ?? null,
                    'amount'          => $newAmount,
                    'payment_type'    => $newPayment['type'],
                    'reference_no'    => $request->reference_no ?? null,
                    'receipt_no'      => $request->receipt_no ?? null,
                    'date'            => $request->date,
                    'description'     => $request->description ?? null,
                ]);

                $newParty = Party::find($request->party_id);
                if ($newParty) {
                    $newParty->opening_balance = (float) ($newParty->opening_balance ?? 0) - $newAmount;
                    $newParty->save();
                }

                if ($paymentIn->bank_account_id) {
                    $newBank = BankAccount::find($paymentIn->bank_account_id);
                    if ($newBank) {
                        $newBank->opening_balance = (float) ($newBank->opening_balance ?? 0) + $newAmount;
                        $newBank->save();
                    }
                }

                $transaction = $this->findMatchingTransaction($paymentIn, $oldAmount);
                if ($transaction) {
                    $transaction->update([
                        'party_id'        => $request->party_id,
                        'date'            => $request->date,
                        'total'           => $newAmount,
                        'paid_amount'     => $newAmount,
                        'debit'           => $newAmount,
                        'credit'          => 0,
                        'status'          => 'receive',
                        'description'     => trim('Payment In'
                            . (($request->reference_no ?? null) ? ' | Ref: ' . $request->reference_no : '')
                            . (($request->receipt_no ?? null) ? ' | Receipt: ' . $request->receipt_no : '')
                        ),
                    ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully!',
                'redirect_url' => route('payment-in'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(PaymentIn $paymentIn)
    {
        try {
            DB::transaction(function () use ($paymentIn) {
                $amount = (float) ($paymentIn->amount ?? 0);
                $party = Party::find($paymentIn->party_id);
                if ($party) {
                    $party->opening_balance = (float) ($party->opening_balance ?? 0) + $amount;
                    $party->save();
                }

                if ($paymentIn->bank_account_id) {
                    $bank = BankAccount::find($paymentIn->bank_account_id);
                    if ($bank) {
                        $bank->opening_balance = (float) ($bank->opening_balance ?? 0) - $amount;
                        $bank->save();
                    }
                }

                $transaction = $this->findMatchingTransaction($paymentIn, $amount);
                if ($transaction) {
                    $transaction->delete();
                }

                $paymentIn->delete();
            });

            return redirect()->route('payment-in')->with('success', 'Payment deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('payment-in')->with('error', $e->getMessage());
        }
    }

    public function print(PaymentIn $paymentIn)
    {
        return view('invoice.print', ['paymentIn' => $paymentIn->load(['party', 'bankAccount'])]);
    }

    public function pdf(PaymentIn $paymentIn)
    {
        return redirect()->route('invoice.payment-in', ['payment_in' => $paymentIn->id]);
    }

    private function findMatchingTransaction(PaymentIn $paymentIn, float $amount): ?Transaction
    {
        return Transaction::query()
            ->where('party_id', $paymentIn->party_id)
            ->where('type', 'receive')
            ->where('total', $amount)
            ->where('date', $paymentIn->date)
            ->where('status', 'receive')
            ->latest('id')
            ->first();
    }

    public function getHistory(PaymentIn $paymentIn)
    {
        try {
            $paymentIn->load(['party', 'bankAccount']);

            // Build history entries - always at least one entry
            $history = [
                [
                    'action' => 'Payment Record Created',
                    'amount' => $paymentIn->amount,
                    'reference' => $paymentIn->reference_no,
                    'receipt' => $paymentIn->receipt_no,
                    'payment_type' => $paymentIn->payment_type,
                    'party' => $paymentIn->party?->name,
                    'bank' => $paymentIn->bankAccount?->display_name,
                    'created_at' => $paymentIn->created_at ? $paymentIn->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                    'user_name' => auth()->user()->name ?? 'System User',
                ]
            ];

            // Get related transactions for this payment
            $transactions = Transaction::where('party_id', $paymentIn->party_id)
                ->where('type', 'receive')
                ->where('status', 'receive')
                ->whereDate('date', $paymentIn->date)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            if ($transactions->count() > 0) {
                foreach ($transactions as $transaction) {
                    $history[] = [
                        'action' => 'Bank Transaction Recorded',
                        'amount' => $transaction->total ?? $transaction->debit,
                        'reference' => $transaction->reference_no ?? '-',
                        'status' => $transaction->status,
                        'description' => $transaction->description,
                        'created_at' => $transaction->created_at ? $transaction->created_at->format('Y-m-d H:i:s') : '-',
                        'user_name' => 'Bank System',
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'history' => $history,
                'total_records' => count($history),
                'payment_details' => [
                    'reference_no' => $paymentIn->reference_no ?? '-',
                    'receipt_no' => $paymentIn->receipt_no ?? '-',
                    'amount' => number_format($paymentIn->amount, 2),
                    'payment_type' => ucfirst($paymentIn->payment_type),
                    'date' => $paymentIn->date,
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Payment History Error: ' . $e->getMessage());
            return response()->json([
                'success' => true,
                'history' => [[
                    'action' => 'Error loading full history',
                    'created_at' => now()->format('Y-m-d H:i:s'),
                    'user_name' => 'System',
                ]],
                'total_records' => 1,
                'message' => 'Showing basic information. Full history unavailable.',
            ]);
        }
    }
}
