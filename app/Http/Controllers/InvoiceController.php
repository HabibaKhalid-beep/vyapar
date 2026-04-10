<?php

namespace App\Http\Controllers;
use App\Models\PaymentIn;
use Illuminate\Http\Request;
use App\Models\BankAccount;
use App\Models\Sale;
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


        $paymentIn = null;
        $allPaymentIns = [];

        if ($request->filled('payment_in')) {
            $paymentInRecord = PaymentIn::with(['party', 'bankAccount'])
                ->find($request->integer('payment_in'));

            if ($paymentInRecord) {
                // Transform PaymentIn into invoice format
                $paymentIn = [
                    'id' => $paymentInRecord->id,
                    'invoice_number' => $paymentInRecord->receipt_no ?? $paymentInRecord->id,
                    'date' => $paymentInRecord->date,
                    'party' => $paymentInRecord->party,
                    'bank_account' => $paymentInRecord->bankAccount,
                    'amount' => $paymentInRecord->amount,
                    'payment_type' => $paymentInRecord->payment_type,
                    'reference_no' => $paymentInRecord->reference_no,
                    'receipt_no' => $paymentInRecord->receipt_no,
                    'description' => $paymentInRecord->description,
                    'items' => [
                        [
                            'name' => $paymentInRecord->payment_type . ' Payment',
                            'quantity' => 1,
                            'price' => $paymentInRecord->amount,
                            'amount' => $paymentInRecord->amount,
                        ]
                    ],
                ];
            }
        }

        // Get all payment invoices
        $allPaymentIns = PaymentIn::with(['party', 'bankAccount'])->latest()->get();

        return view('invoice.index', compact('reactCss', 'reactJs', 'paymentIn', 'allPaymentIns'));

        $sale = null;
        $invoiceAppData = [
            'saleId' => null,
            'invoiceData' => null,
            'initialTheme' => (string) $request->query('theme', 'tally'),
            'initialColor' => (string) $request->query('accent', '#707070'),
            'invoicePdfUrl' => null,
        ];

        if ($request->filled('sale_id')) {
            $sale = Sale::with(['items.item', 'party', 'payments.bankAccount'])->findOrFail($request->integer('sale_id'));

            $invoiceAppData = [
                'saleId' => $sale->id,
                'invoiceData' => $this->mapSaleToReactInvoiceData($sale),
                'initialTheme' => (string) $request->query('theme', 'tally'),
                'initialColor' => (string) $request->query('accent', '#707070'),
                'invoicePdfUrl' => route('sale.invoice-pdf', $sale),
            ];
        }

        return view('invoice.index', compact('reactCss', 'reactJs', 'invoiceAppData'));

    }

    public function print()
    {
        return view('invoice.print');
    }


    public function paymentIn(Request $request)
    {
        $reactCss = collect(glob(public_path('react-invoice/assets/index-*.css')))
            ->sortByDesc(fn ($path) => filemtime($path))
            ->map(fn ($path) => asset('react-invoice/assets/' . basename($path)))
            ->first();

        $reactJs = collect(glob(public_path('react-invoice/assets/index-*.js')))
            ->sortByDesc(fn ($path) => filemtime($path))
            ->map(fn ($path) => asset('react-invoice/assets/' . basename($path)))
            ->first();

        $paymentIn = null;
        $allPaymentIns = [];

        if ($request->filled('payment_in')) {
            $paymentInRecord = PaymentIn::with(['party', 'bankAccount'])
                ->find($request->integer('payment_in'));

            if ($paymentInRecord) {
                // Transform PaymentIn into invoice format
                $paymentIn = [
                    'id' => $paymentInRecord->id,
                    'invoice_number' => $paymentInRecord->receipt_no ?? $paymentInRecord->id,
                    'date' => $paymentInRecord->date,
                    'party' => $paymentInRecord->party,
                    'bank_account' => $paymentInRecord->bankAccount,
                    'amount' => $paymentInRecord->amount,
                    'payment_type' => $paymentInRecord->payment_type,
                    'reference_no' => $paymentInRecord->reference_no,
                    'receipt_no' => $paymentInRecord->receipt_no,
                    'description' => $paymentInRecord->description,
                    'items' => [
                        [
                            'name' => $paymentInRecord->payment_type . ' Payment',
                            'quantity' => 1,
                            'price' => $paymentInRecord->amount,
                            'amount' => $paymentInRecord->amount,
                        ]
                    ],
                ];
            }
        }

        // Get all payment invoices
        $allPaymentIns = PaymentIn::with(['party', 'bankAccount'])->latest()->get();

        return view('invoice.payment-in', compact('reactCss', 'reactJs', 'paymentIn', 'allPaymentIns'));
    }

    private function mapSaleToReactInvoiceData(Sale $sale): array
    {
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

        return [
            'title' => $sale->type === 'invoice' ? 'Invoice' : ucwords(str_replace('_', ' ', (string) $sale->type)),
            'businessName' => (string) config('app.name', 'My Company'),
            'businessPhone' => (string) ($bankAccount?->phone ?: ''),
            'invoiceNo' => (string) ($sale->bill_number ?: $sale->id),
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
            'total' => (float) ($sale->grand_total ?? 0),
            'received' => (float) ($sale->received_amount ?? 0),
            'balance' => (float) ($sale->balance ?? 0),
            'items' => $items,
            'bankName' => (string) ($bankAccount?->bank_name ?: $bankAccount?->display_name ?: ''),
            'bankAccountNumber' => (string) ($bankAccount?->account_number ?: ''),
            'bankAccountHolder' => (string) ($bankAccount?->account_holder_name ?: ''),
        ];

    }
}
