<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\PaymentIn;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $reactCss = collect(glob(public_path('react-invoice/assets/index-*.css')))
            ->sortByDesc(fn ($path) => filemtime($path))
            ->map(fn ($path) => asset('react-invoice/assets/' . basename($path)))
            ->first();

        $reactJs = collect(glob(public_path('react-invoice/assets/index-*.js')))
            ->sortByDesc(fn ($path) => filemtime($path))
            ->map(fn ($path) => asset('react-invoice/assets/' . basename($path)))
            ->first();

        $invoiceAppData = [
            'saleId' => null,
            'invoiceData' => null,
            'initialTheme' => (string) $request->query('theme', 'tally'),
            'initialColor' => (string) $request->query('accent', '#707070'),
            'invoicePdfUrl' => null,
            'browserTabLabel' => 'Invoice Preview',
            'saveCloseUrl' => route('sale.index'),
        ];

        $paymentIn = null;
        $allPaymentIns = collect();

        if ($request->filled('sale_id')) {
            $sale = Sale::with(['items.item', 'party', 'broker', 'challanDetail', 'payments.bankAccount'])

                ->findOrFail($request->integer('sale_id'));

            $docType = $request->query('doc');
            $invoiceSource = $sale;

            if ($docType === 'delivery_challan') {
                if ($sale->type === 'delivery_challan') {
                    $invoiceSource = $sale;
                } elseif ($sale->reference_id) {
                    $sourceChallan = Sale::with(['items.item', 'party', 'challanDetail'])
                        ->whereKey($sale->reference_id)
                        ->where('type', 'delivery_challan')
                        ->first();
                    if ($sourceChallan) {
                        $invoiceSource = $sourceChallan;
                    }
                }
            }

            $invoiceAppData = [
                'saleId' => $sale->id,
                'invoiceData' => $this->mapSaleToReactInvoiceData($invoiceSource),
                'initialTheme' => (string) $request->query('theme', 'tally'),
                'initialColor' => (string) $request->query('accent', '#707070'),
                'invoicePdfUrl' => route('sale.invoice-pdf', $sale),
                'browserTabLabel' => ($invoiceSource->type === 'delivery_challan' ? 'Delivery Challan' : 'Invoice') . ' #' . ($invoiceSource->bill_number ?: $invoiceSource->id),
                'saveCloseUrl' => route('sale.index'),
            ];
        } elseif ($request->filled('payment_in')) {
            $paymentInRecord = PaymentIn::with(['party', 'bankAccount'])
                ->findOrFail($request->integer('payment_in'));

            $paymentIn = $this->mapPaymentInLegacyData($paymentInRecord);
            $invoiceAppData = [
                'saleId' => $paymentInRecord->id,
                'invoiceData' => $this->mapPaymentInToReactInvoiceData($paymentInRecord),
                'initialTheme' => (string) $request->query('theme', 'tally'),
                'initialColor' => (string) $request->query('accent', '#707070'),
                'invoicePdfUrl' => null,
                'browserTabLabel' => 'Receipt #' . ($paymentInRecord->receipt_no ?: $paymentInRecord->id),
                'saveCloseUrl' => route('payment-in'),
            ];
            $allPaymentIns = PaymentIn::with(['party', 'bankAccount'])->latest()->get();
        }

        return view('invoice.index', [
            'reactCss' => $reactCss,
            'reactJs' => $reactJs,
            'invoiceAppData' => $invoiceAppData,
            'paymentIn' => $paymentIn,
            'allPaymentIns' => $allPaymentIns,
        ]);
    }

    public function print()
    {
        return view('invoice.print');
    }

    public function proforma(Request $request, Sale $sale)
    {
        abort_unless($sale->type === 'proforma', 404);

        $reactCss = collect(glob(public_path('react-invoice/assets/index-*.css')))
            ->sortByDesc(fn ($path) => filemtime($path))
            ->map(fn ($path) => asset('react-invoice/assets/' . basename($path)))
            ->first();

        $reactJs = collect(glob(public_path('react-invoice/assets/index-*.js')))
            ->sortByDesc(fn ($path) => filemtime($path))
            ->map(fn ($path) => asset('react-invoice/assets/' . basename($path)))
            ->first();

        $sale->loadMissing(['items.item', 'party', 'payments.bankAccount']);
        $invoiceData = $this->mapSaleToReactInvoiceData($sale);
        $invoiceData['title'] = 'Proforma Invoice';

        $invoiceAppData = [
            'saleId' => $sale->id,
            'invoiceData' => $invoiceData,
            'initialTheme' => (string) $request->query('theme', 'tally'),
            'initialColor' => (string) $request->query('accent', '#707070'),
            'invoicePdfUrl' => null,
            'browserTabLabel' => 'Proforma #' . ($sale->bill_number ?: $sale->id),
            'saveCloseUrl' => route('proforma-invoice'),
        ];

        return view('invoice.proforma', [
            'reactCss' => $reactCss,
            'reactJs' => $reactJs,
            'invoiceAppData' => $invoiceAppData,
            'paymentIn' => null,
            'allPaymentIns' => collect(),
        ]);
    }

    public function paymentIn(Request $request)
    {
        return $this->index($request);
    }

    private function mapSaleToReactInvoiceData(Sale $sale): array
    {
        $sale->loadMissing(['challanDetail']);
        $bankAccount = $sale->payments
            ->pluck('bankAccount')
            ->filter()
            ->first();

        if (!$bankAccount) {
            $bankAccount = BankAccount::where('print_on_invoice', true)
                ->orderBy('id')
                ->first();
        }

        $items = $sale->items->map(function ($item) {
            return [
                'name' => (string) ($item->item_name ?: ($item->item?->name ?: 'Item')),
                'hsn' => (string) ($item->item_code ?: ($item->item?->item_code ?: '')),
                'qty' => (float) ($item->quantity ?? 0),
                'unit' => (string) ($item->unit ?: ($item->item?->unit ?: '')),
                'rate' => (float) ($item->unit_price ?? 0),
                'discount' => (float) ($item->discount ?? 0),
                'amount' => (float) ($item->amount ?? 0),
            ];
        })->values()->all();

        $createdAt = $sale->created_at instanceof Carbon ? $sale->created_at : Carbon::parse($sale->created_at);
        $invoiceDate = $sale->invoice_date ? Carbon::parse($sale->invoice_date) : $createdAt;
        $challanDetail = $sale->challanDetail;
        $partyCity = (string) ($sale->party?->city ?: '');
        if ($sale->type === 'delivery_challan' && $sale->challanDetail?->invoice_date) {
            $invoiceDate = Carbon::parse($sale->challanDetail->invoice_date);
        }

        $paymentsReceived = (float) $sale->payments
            ->sum('amount');

        $totalAmount = (float) ($sale->grand_total ?? 0);
        $storedBalance = (float) ($sale->balance ?? 0);

        $receivedAmount = (float) ($sale->received_amount ?? 0);
        $receivedFromBalance = $totalAmount > 0 ? max($totalAmount - $storedBalance, 0) : 0;
        $receivedAmount = max($receivedAmount, $paymentsReceived, $receivedFromBalance);

        $invoiceNumber = $sale->bill_number ?: $sale->id;
        if ($sale->type === 'delivery_challan' && $sale->challanDetail?->challan_number) {
            $invoiceNumber = $sale->challanDetail->challan_number;
        }

        return [
            'title' => $sale->type === 'invoice' ? 'Invoice' : ucwords(str_replace('_', ' ', (string) $sale->type)),
            'businessName' => (string) config('app.name', 'My Company'),
            'businessPhone' => (string) ($bankAccount?->phone ?: ''),
            'invoiceNo' => (string) $invoiceNumber,
            'date' => $invoiceDate->format('d/m/Y'),
            'time' => $createdAt->format('h:i A'),
            'billTo' => (string) ($sale->display_party_name !== '-' ? $sale->display_party_name : 'Walk-in Customer'),
            'billAddress' => (string) ($sale->billing_address ?: ''),
            'billPhone' => (string) ($sale->phone ?: ($sale->party?->phone ?: '')),
            'shipTo' => (string) ($sale->shipping_address ?: $sale->billing_address ?: ''),
            'description' => (string) ($sale->description ?: 'Thanks for doing business with us!'),
            'subtotal' => (float) ($sale->total_amount ?? 0),
            'discount' => (float) ($sale->discount_rs ?? 0),
            'taxAmount' => (float) ($sale->tax_amount ?? 0),
            'total' => $totalAmount,
            'received' => $receivedAmount,
            'balance' => (float) ($sale->balance ?? max($totalAmount - $receivedAmount, 0)),
            'items' => $items,
            'bankName' => (string) ($bankAccount?->bank_name ?: $bankAccount?->display_name ?: ''),
            'bankAccountNumber' => (string) ($bankAccount?->account_number ?: ''),
            'bankAccountHolder' => (string) ($bankAccount?->account_holder_name ?: ''),
            'brokerName' => (string) ($challanDetail?->broker_name ?: $sale->broker?->name ?: ''),
            'brokerPhone' => (string) ($challanDetail?->broker_phone ?: $sale->broker?->phone ?: ''),
            'city' => $partyCity,
            'warehouseName' => (string) ($challanDetail?->warehouse_name ?: ''),
            'holderName' => (string) ($challanDetail?->warehouse_handler_name ?: ''),
        ];
    }

    private function mapPaymentInToReactInvoiceData(PaymentIn $paymentIn): array
    {
        $createdAt = $paymentIn->created_at instanceof Carbon
            ? $paymentIn->created_at
            : Carbon::parse($paymentIn->created_at ?? now());

        $date = $paymentIn->date ? Carbon::parse($paymentIn->date) : $createdAt;
        $amount = (float) ($paymentIn->amount ?? 0);

        return [
            'title' => 'Payment In Invoice',
            'businessName' => (string) config('app.name', 'My Company'),
            'businessPhone' => (string) ($paymentIn->bankAccount?->phone ?: ''),
            'invoiceNo' => (string) ($paymentIn->receipt_no ?: $paymentIn->id),
            'date' => $date->format('d/m/Y'),
            'time' => $createdAt->format('h:i A'),
            'billTo' => (string) ($paymentIn->party?->name ?: 'Customer'),
            'billAddress' => (string) ($paymentIn->party?->billing_address ?: ''),
            'billPhone' => (string) ($paymentIn->party?->phone ?: ''),
            'shipTo' => (string) ($paymentIn->party?->billing_address ?: ''),
            'description' => (string) ($paymentIn->description ?: 'Payment received.'),
            'subtotal' => $amount,
            'discount' => 0,
            'taxAmount' => 0,
            'total' => $amount,
            'received' => $amount,
            'balance' => 0,
            'items' => [[
                'name' => (string) (($paymentIn->payment_type ?: 'Payment') . ' Payment'),
                'hsn' => (string) ($paymentIn->reference_no ?: ''),
                'qty' => 1,
                'unit' => '',
                'rate' => $amount,
                'discount' => 0,
                'amount' => $amount,
            ]],
            'bankName' => (string) ($paymentIn->bankAccount?->bank_name ?: $paymentIn->bankAccount?->display_name ?: ''),
            'bankAccountNumber' => (string) ($paymentIn->bankAccount?->account_number ?: ''),
            'bankAccountHolder' => (string) ($paymentIn->bankAccount?->account_holder_name ?: ''),
        ];
    }

    private function mapPaymentInLegacyData(PaymentIn $paymentIn): array
    {
        return [
            'id' => $paymentIn->id,
            'invoice_number' => $paymentIn->receipt_no ?? $paymentIn->id,
            'date' => $paymentIn->date,
            'party' => $paymentIn->party,
            'bank_account' => $paymentIn->bankAccount,
            'amount' => $paymentIn->amount,
            'payment_type' => $paymentIn->payment_type,
            'reference_no' => $paymentIn->reference_no,
            'receipt_no' => $paymentIn->receipt_no,
            'description' => $paymentIn->description,
            'items' => [[
                'name' => ($paymentIn->payment_type ?: 'Payment') . ' Payment',
                'quantity' => 1,
                'price' => $paymentIn->amount,
                'amount' => $paymentIn->amount,
            ]],
        ];
    }
}
