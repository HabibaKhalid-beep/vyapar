<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Party;
use App\Models\Transaction;
use App\Models\BankAccount;
use App\Models\PaymentIn;
use App\Models\PaymentInLink;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PaymentInController extends Controller
{
    public function index(Request $request)
    {
        return view('dashboard.sales.payement-in', [
            'parties'      => Party::all(),
            'bankAccounts' => BankAccount::active()->get(),
            'paymentIns'   => PaymentIn::with(['party', 'bankAccount'])->latest()->get(),
            'editPaymentIn' => $request->filled('edit_payment_in')
                ? PaymentIn::with(['party', 'bankAccount', 'links.sale'])->find($request->integer('edit_payment_in'))
                : null,
        ]);
    }

    public function linkableSales(Request $request, Party $party)
    {
        $paymentInId = $request->integer('payment_in_id');
        $existingLinks = collect();

        if ($paymentInId > 0) {
            $existingLinks = PaymentInLink::query()
                ->selectRaw('sale_id, SUM(linked_amount) as linked_amount')
                ->where('payment_in_id', $paymentInId)
                ->groupBy('sale_id')
                ->pluck('linked_amount', 'sale_id');
        }

        $sales = Sale::query()
            ->where('party_id', $party->id)
            ->whereIn('type', ['invoice', 'pos'])
            ->where(function ($query) use ($existingLinks) {
                $query->where('balance', '>', 0);

                if ($existingLinks->isNotEmpty()) {
                    $query->orWhereIn('id', $existingLinks->keys());
                }
            })
            ->orderByDesc('invoice_date')
            ->orderByDesc('id')
            ->get()
            ->map(function (Sale $sale) use ($existingLinks) {
                $existingAmount = (float) ($existingLinks[$sale->id] ?? 0);
                $currentBalance = (float) ($sale->balance ?? max(0, (float) ($sale->grand_total ?? 0) - (float) ($sale->received_amount ?? 0)));
                $availableBalance = $currentBalance + $existingAmount;

                return [
                    'sale_id' => $sale->id,
                    'date' => optional($sale->invoice_date)->format('d/m/Y') ?: '-',
                    'type' => strtoupper($sale->type === 'pos' ? 'POS' : 'Sale'),
                    'ref_no' => $sale->bill_number ?: ('INV-' . $sale->id),
                    'total' => round((float) ($sale->grand_total ?? $sale->total_amount ?? 0), 2),
                    'balance' => round($availableBalance, 2),
                    'linked_amount' => round($existingAmount, 2),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'party' => [
                'id' => $party->id,
                'name' => $party->name,
            ],
            'rows' => $sales,
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
            'linked_rows'                => 'nullable|array',
            'linked_rows.*.sale_id'      => 'required|exists:sales,id',
            'linked_rows.*.amount'       => 'required|numeric|min:0.01',
        ]);

        try {
            $party = Party::findOrFail($request->party_id);
            $savedPayments = collect();

            DB::transaction(function () use ($request, $party, &$savedPayments) {
                $this->validateLinkedRows($request, collect());

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

                $this->attachLinkedRows($savedPayments, $request->input('linked_rows', []));
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
            'linked_rows'                => 'nullable|array',
            'linked_rows.*.sale_id'      => 'required|exists:sales,id',
            'linked_rows.*.amount'       => 'required|numeric|min:0.01',
        ]);

        try {
            DB::transaction(function () use ($request, $paymentIn) {
                $oldAmount = (float) ($paymentIn->amount ?? 0);
                $newPayment = $request->payments[0];
                $newAmount = (float) ($newPayment['amount'] ?? 0);
                $existingLinks = $paymentIn->links()->get();

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

                $this->rollbackLinkedRows($existingLinks);
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

                $this->validateLinkedRows($request, $existingLinks);
                $this->attachLinkedRows(collect([$paymentIn->fresh()]), $request->input('linked_rows', []));
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
                $existingLinks = $paymentIn->links()->get();
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

                $this->rollbackLinkedRows($existingLinks);
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

    private function validateLinkedRows(Request $request, Collection $existingLinks): void
    {
        $linkedRows = collect($request->input('linked_rows', []))
            ->map(function ($row) {
                return [
                    'sale_id' => (int) ($row['sale_id'] ?? 0),
                    'amount' => round((float) ($row['amount'] ?? 0), 2),
                ];
            })
            ->filter(fn ($row) => $row['sale_id'] > 0 && $row['amount'] > 0)
            ->values();

        if ($linkedRows->isEmpty()) {
            return;
        }

        $paymentsTotal = collect($request->input('payments', []))
            ->sum(fn ($payment) => (float) ($payment['amount'] ?? 0));
        $linkedTotal = $linkedRows->sum('amount');

        if ($linkedTotal - $paymentsTotal > 0.001) {
            throw ValidationException::withMessages([
                'linked_rows' => ['Linked amount received amount se zyada nahi ho sakta.'],
            ]);
        }

        $existingBySale = $existingLinks
            ->groupBy('sale_id')
            ->map(fn ($rows) => (float) $rows->sum('linked_amount'));

        foreach ($linkedRows as $row) {
            $sale = Sale::query()
                ->whereKey($row['sale_id'])
                ->whereIn('type', ['invoice', 'pos'])
                ->first();

            if (!$sale) {
                throw ValidationException::withMessages([
                    'linked_rows' => ['Selected transaction available nahi hai.'],
                ]);
            }

            $availableBalance = (float) ($sale->balance ?? 0) + (float) ($existingBySale[$sale->id] ?? 0);
            if ($row['amount'] - $availableBalance > 0.001) {
                throw ValidationException::withMessages([
                    'linked_rows' => ['Linked amount selected transaction ke balance se zyada hai.'],
                ]);
            }
        }
    }

    private function attachLinkedRows(Collection $paymentIns, array $linkedRows): void
    {
        $payments = $paymentIns->values();
        if ($payments->isEmpty()) {
            return;
        }

        $rows = collect($linkedRows)
            ->map(function ($row) {
                return [
                    'sale_id' => (int) ($row['sale_id'] ?? 0),
                    'amount' => round((float) ($row['amount'] ?? 0), 2),
                ];
            })
            ->filter(fn ($row) => $row['sale_id'] > 0 && $row['amount'] > 0)
            ->values();

        if ($rows->isEmpty()) {
            return;
        }

        $paymentIndex = 0;
        $remainingPaymentAmount = (float) ($payments[$paymentIndex]->amount ?? 0);

        foreach ($rows as $row) {
            $sale = Sale::query()->lockForUpdate()->findOrFail($row['sale_id']);
            $remainingLinkAmount = (float) $row['amount'];

            while ($remainingLinkAmount > 0.001) {
                while ($paymentIndex < $payments->count() && $remainingPaymentAmount <= 0.001) {
                    $paymentIndex++;
                    $remainingPaymentAmount = $paymentIndex < $payments->count()
                        ? (float) ($payments[$paymentIndex]->amount ?? 0)
                        : 0;
                }

                if ($paymentIndex >= $payments->count()) {
                    throw new \RuntimeException('Linked amount ko save karne ke liye enough payment rows available nahi hain.');
                }

                $allocate = min($remainingLinkAmount, $remainingPaymentAmount);

                PaymentInLink::create([
                    'payment_in_id' => $payments[$paymentIndex]->id,
                    'sale_id' => $sale->id,
                    'linked_amount' => $allocate,
                ]);

                $remainingLinkAmount -= $allocate;
                $remainingPaymentAmount -= $allocate;
            }

            $this->applySaleLinkDelta($sale, (float) $row['amount']);
        }
    }

    private function rollbackLinkedRows(Collection $links): void
    {
        if ($links->isEmpty()) {
            return;
        }

        $grouped = $links->groupBy('sale_id');

        foreach ($grouped as $saleId => $saleLinks) {
            $sale = Sale::query()->lockForUpdate()->find($saleId);
            if ($sale) {
                $this->applySaleLinkDelta($sale, -1 * (float) $saleLinks->sum('linked_amount'));
            }
        }

        PaymentInLink::query()
            ->whereIn('id', $links->pluck('id'))
            ->delete();
    }

    private function applySaleLinkDelta(Sale $sale, float $delta): void
    {
        $receivedAmount = max(0, (float) ($sale->received_amount ?? 0) + $delta);
        $grandTotal = (float) ($sale->grand_total ?? $sale->total_amount ?? 0);
        $balance = max(0, $grandTotal - $receivedAmount);
        $status = $balance <= 0.001 ? 'paid' : ($receivedAmount > 0 ? 'partially paid' : 'unpaid');

        $sale->update([
            'received_amount' => round($receivedAmount, 2),
            'balance' => round($balance, 2),
            'status' => $status,
        ]);
    }
}
