<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\PaymentIn;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        return view('invoice.index', $this->buildInvoiceViewData($request));
    }

    public function downloadPdf(Request $request)
    {
        $viewData = $this->buildInvoiceViewData($request);
        $saleId = (int) ($viewData['sale']->id ?? $request->integer('sale_id'));

        abort_unless($saleId > 0, 404);

        $htmlDirectory = storage_path('app/invoice-pdf');
        File::ensureDirectoryExists($htmlDirectory);

        $htmlPath = $htmlDirectory . DIRECTORY_SEPARATOR . 'invoice-' . $saleId . '-' . uniqid() . '.html';
        $pdfPath = $htmlDirectory . DIRECTORY_SEPARATOR . 'invoice-' . $saleId . '-' . uniqid() . '.pdf';

        $viewData['reactCss'] = url('/react-invoice/assets/index-7A0P_pSc.css');
        $viewData['reactJs'] = url('/react-invoice/assets/index-B2etBuUm.js');
        $viewData['pdfDirectDownload'] = true;
        $viewData['reactCssInline'] = File::get(public_path('react-invoice/assets/index-7A0P_pSc.css'));
        $viewData['reactJsInline'] = File::get(public_path('react-invoice/assets/index-B2etBuUm.js'));
        $viewData['saveCloseUrl'] = route('sale.index');

        File::put($htmlPath, view('invoice.index', $viewData)->render());

        $chromePath = $this->resolveChromeExecutable();
        abort_unless($chromePath !== null, 500, 'Chrome/Edge executable not found for PDF generation.');

        $process = new Process([
            $chromePath,
            '--headless=new',
            '--disable-gpu',
            '--disable-extensions',
            '--disable-sync',
            '--no-pdf-header-footer',
            '--run-all-compositor-stages-before-draw',
            '--virtual-time-budget=1200',
            '--print-to-pdf=' . $pdfPath,
            'file:///' . str_replace('\\', '/', $htmlPath),
        ]);

        $process->setTimeout(60);
        $process->run();

        File::delete($htmlPath);

        if (! $process->isSuccessful() || ! File::exists($pdfPath)) {
            File::delete($pdfPath);
            abort(500, 'PDF generation failed.');
        }

        $downloadName = 'invoice-' . ($viewData['invoicePreviewData']['invoiceNo'] ?? $saleId) . '.pdf';

        return response()->download($pdfPath, $downloadName)->deleteFileAfterSend(true);
    }

    public function print()
    {
        return view('invoice.print');
    }

    public function proforma(Request $request, Sale $sale)
    {
        abort_unless($sale->type === 'proforma', 404);

        $sale->loadMissing(['items.item', 'party', 'payments.bankAccount']);
        $invoicePreviewData = $this->mapSaleToThemePreviewData($sale);
        $invoicePreviewData['title'] = 'Proforma Invoice';

        return view('invoice.proforma', [
            'invoicePreviewData' => $invoicePreviewData,
            'pageTitle' => 'Proforma Preview',
            'browserTabLabel' => 'Proforma #' . ($sale->bill_number ?: $sale->id),
            'saveCloseUrl' => route('proforma-invoice'),
            'initialMode' => (string) $request->query('mode', 'regular'),
            'initialRegularThemeId' => (int) $request->query('theme_id', 1),
            'initialThermalThemeId' => (int) $request->query('theme_id', 1),
            'initialAccent' => (string) $request->query('accent', '#1f4e79'),
            'initialAccent2' => (string) $request->query('accent2', '#ff981f'),
        ]);
    }

    public function paymentIn(Request $request)
    {
        return $this->index($request);
    }

    private function buildInvoiceViewData(Request $request): array
    {
        $selectedTheme = (string) $request->query('theme', 'tally');
        $selectedColor = (string) $request->query('color', '#707070');
        $selectedColor2 = (string) $request->query('color2', '#ff981f');

        $viewData = [
            'invoicePreviewData' => [],
            'pageTitle' => 'Preview',
            'browserTabLabel' => 'Invoice Preview',
            'saveCloseUrl' => route('sale.index'),
            'initialMode' => $selectedTheme,
            'initialRegularThemeId' => (int) $request->query('theme_id', 1),
            'initialThermalThemeId' => (int) $request->query('theme_id', 1),
            'initialAccent' => $selectedColor,
            'initialAccent2' => $selectedColor2,
            'reactCss' => asset('react-invoice/assets/index-7A0P_pSc.css'),
            'reactJs' => asset('react-invoice/assets/index-B2etBuUm.js'),
        ];

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

            $viewData['sale'] = $sale;
            $viewData['invoicePreviewData'] = $this->mapSaleToThemePreviewData($invoiceSource);
            $viewData['browserTabLabel'] = ($invoiceSource->type === 'delivery_challan' ? 'Delivery Challan' : 'Invoice') . ' #' . ($invoiceSource->bill_number ?: $invoiceSource->id);
        } elseif ($request->filled('payment_in')) {
            $paymentInRecord = PaymentIn::with(['party', 'bankAccount'])
                ->findOrFail($request->integer('payment_in'));

            $viewData['invoicePreviewData'] = $this->mapPaymentInToThemePreviewData($paymentInRecord);
            $viewData['browserTabLabel'] = 'Receipt #' . ($paymentInRecord->receipt_no ?: $paymentInRecord->id);
            $viewData['saveCloseUrl'] = route('payment-in');
        }

        return $viewData;
    }

    private function resolveChromeExecutable(): ?string
    {
        $candidates = [
            'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
            'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe',
            'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function mapSaleToThemePreviewData(Sale $sale): array
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

        $taxPct = $this->formatPercentValue($sale->tax_pct);

        $items = $sale->items->map(function ($item) use ($taxPct) {
            return [
                'name' => (string) ($item->item_name ?: ($item->item?->name ?: 'Item')),
                'hsn' => (string) ($item->item_code ?: ($item->item?->item_code ?: '')),
                'qty' => (string) ($item->quantity ?? 0),
                'unit' => (string) ($item->unit ?: ($item->item?->unit ?: '')),
                'rate' => (float) ($item->unit_price ?? 0),
                'disc' => number_format((float) ($item->discount ?? 0), 2, '.', ''),
                'gst' => $taxPct,
                'amt' => (float) ($item->amount ?? 0),
            ];
        })->values()->all();

        if (empty($items)) {
            $items[] = [
                'name' => 'Item',
                'hsn' => '',
                'qty' => '0',
                'unit' => '',
                'rate' => 0,
                'disc' => '0.00',
                'gst' => $taxPct,
                'amt' => 0,
            ];
        }

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
            'phone' => (string) ($sale->phone ?: ($sale->party?->phone ?: '')),
            'invoiceNo' => (string) $invoiceNumber,
            'date' => $invoiceDate->format('d/m/Y'),
            'time' => $createdAt->format('h:i A'),
            'dueDate' => ($sale->due_date ? Carbon::parse($sale->due_date) : $invoiceDate)->format('d/m/Y'),
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

    private function mapPaymentInToThemePreviewData(PaymentIn $paymentIn): array
    {
        $createdAt = $paymentIn->created_at instanceof Carbon
            ? $paymentIn->created_at
            : Carbon::parse($paymentIn->created_at ?? now());

        $date = $paymentIn->date ? Carbon::parse($paymentIn->date) : $createdAt;
        $amount = (float) ($paymentIn->amount ?? 0);

        return [
            'title' => 'Payment In Invoice',
            'businessName' => (string) config('app.name', 'My Company'),
            'phone' => (string) ($paymentIn->bankAccount?->phone ?: ''),
            'invoiceNo' => (string) ($paymentIn->receipt_no ?: $paymentIn->id),
            'date' => $date->format('d/m/Y'),
            'time' => $createdAt->format('h:i A'),
            'dueDate' => $date->format('d/m/Y'),
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
                'qty' => '1',
                'unit' => '',
                'rate' => $amount,
                'disc' => '0.00',
                'gst' => '0%',
                'amt' => $amount,
            ]],
            'bankName' => (string) ($paymentIn->bankAccount?->bank_name ?: $paymentIn->bankAccount?->display_name ?: ''),
            'bankAccountNumber' => (string) ($paymentIn->bankAccount?->account_number ?: ''),
            'bankAccountHolder' => (string) ($paymentIn->bankAccount?->account_holder_name ?: ''),
        ];
    }

    private function formatPercentValue($value): string
    {
        $number = (float) ($value ?? 0);

        if (fmod($number, 1.0) === 0.0) {
            return (string) ((int) $number) . '%';
        }

        return rtrim(rtrim(number_format($number, 2, '.', ''), '0'), '.') . '%';
    }
}
