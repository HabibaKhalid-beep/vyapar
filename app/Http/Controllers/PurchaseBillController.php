<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Item;
use App\Models\Party;
use App\Models\Purchase;
use App\Support\TransactionNumberPrefix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Process\Process;

class PurchaseBillController extends Controller
{
    public function purchaseExpenses(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $purchases = Purchase::with(['items', 'payments.bankAccount', 'party'])
            ->where('type', 'purchase_bill')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('bill_number', 'like', '%' . $search . '%')
                        ->orWhere('party_name', 'like', '%' . $search . '%')
                        ->orWhereHas('party', function ($partyQuery) use ($search) {
                            $partyQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderByDesc('bill_date')
            ->orderByDesc('created_at')
            ->get();

        $paidTotal = (float) $purchases->sum('paid_amount');
        $unpaidTotal = (float) $purchases->sum('balance');
        $grandTotal = (float) $purchases->sum('grand_total');

        return view('dashboard.purchases.purchase-bill', compact(
            'purchases',
            'search',
            'paidTotal',
            'unpaidTotal',
            'grandTotal'
        ));
    }

    public function create()
    {
        $bankAccounts = BankAccount::active()->orderBy('display_name')->get();
        $items = Item::active()->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();
        $nextInvoiceNumber = TransactionNumberPrefix::format('purchase_bill', (Purchase::where('type', 'purchase')->max('id') ?? 0) + 1);
        $convertedPurchaseData = null;

        $sourcePurchaseOrderId = request()->integer('source_purchase_order_id');
        if ($sourcePurchaseOrderId) {
            $alreadyConvertedPurchase = Purchase::where('type', 'purchase_bill')
                ->where('source_purchase_order_id', $sourcePurchaseOrderId)
                ->first();

            if ($alreadyConvertedPurchase) {
                return redirect()
                    ->route('purchase-order')
                    ->with('error', 'This purchase order is already converted to purchase #' . ($alreadyConvertedPurchase->bill_number ?? $alreadyConvertedPurchase->id));
            }

            $sourcePurchaseOrder = Purchase::with(['items', 'payments'])
                ->where('type', 'purchase_order')
                ->find($sourcePurchaseOrderId);

            if ($sourcePurchaseOrder) {
                $convertedPurchaseData = $sourcePurchaseOrder;
            }
        }

        return view('dashboard.purchases.create-purchase-bill', compact(
            'bankAccounts',
            'items',
            'parties',
            'nextInvoiceNumber',
            'convertedPurchaseData'
        ));
    }

    public function edit(Purchase $purchase)
    {
        $bankAccounts = BankAccount::active()->orderBy('display_name')->get();
        $items = Item::active()->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        $purchase->load(['items', 'payments']);

        return view('dashboard.purchases.create-purchase-bill', compact(
            'bankAccounts',
            'items',
            'parties',
            'purchase'
        ));
    }

    public function store(Request $request)
    {
        $data = $this->validatePurchase($request);
        $purchase = $this->savePurchase(new Purchase(), $data);

        return response()->json([
            'success' => true,
            'purchase_id' => $purchase->id,
            'bill_number' => $purchase->bill_number,
            'redirect_url' => route('purchase-bills.preview', $purchase),
        ]);
    }

    public function update(Request $request, Purchase $purchase)
    {
        $data = $this->validatePurchase($request);
        $purchase = $this->savePurchase($purchase, $data);

        return response()->json([
            'success' => true,
            'purchase_id' => $purchase->id,
            'bill_number' => $purchase->bill_number,
            'redirect_url' => route('purchase-bills.preview', $purchase),
        ]);
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->items()->delete();
        $purchase->payments()->delete();
        $purchase->delete();

        return response()->json([
            'success' => true,
            'message' => 'Purchase bill deleted successfully.',
        ]);
    }

    public function preview(Purchase $purchase)
    {
        $purchase->load(['items', 'payments.bankAccount', 'party']);
        return view('invoice.index', $this->buildPurchaseInvoiceViewData($purchase, [
            'pageTitle' => 'Purchase Preview',
            'browserTabLabel' => 'Purchase #' . ($purchase->bill_number ?: $purchase->id),
        ]));
    }

    public function print(Purchase $purchase)
    {
        $purchase->load(['items', 'payments.bankAccount', 'party']);
        return view('invoice.index', $this->buildPurchaseInvoiceViewData($purchase, [
            'pageTitle' => 'Purchase Print',
            'browserTabLabel' => 'Purchase #' . ($purchase->bill_number ?: $purchase->id),
            'autoPrintPreview' => true,
        ]));
    }

    public function pdf(Purchase $purchase)
    {
        $purchase->load(['items', 'payments.bankAccount', 'party']);
        return view('invoice.index', $this->buildPurchaseInvoiceViewData($purchase, [
            'pageTitle' => 'Purchase PDF',
            'browserTabLabel' => 'Purchase #' . ($purchase->bill_number ?: $purchase->id),
        ]));
    }

    public function downloadPdf(Purchase $purchase)
    {
        $purchase->load(['items', 'payments.bankAccount', 'party']);

        $viewData = $this->buildPurchaseInvoiceViewData($purchase, [
            'pageTitle' => 'Purchase PDF',
            'browserTabLabel' => 'Purchase #' . ($purchase->bill_number ?: $purchase->id),
            'pdfDirectDownload' => true,
            'reactCssInline' => File::get(public_path('react-invoice/assets/index-7A0P_pSc.css')),
            'reactJsInline' => File::get(public_path('react-invoice/assets/index-B2etBuUm.js')),
            'reactCss' => url('/react-invoice/assets/index-7A0P_pSc.css'),
            'reactJs' => url('/react-invoice/assets/index-B2etBuUm.js'),
        ]);

        $htmlDirectory = storage_path('app/purchase-pdf');
        File::ensureDirectoryExists($htmlDirectory);

        $htmlPath = $htmlDirectory . DIRECTORY_SEPARATOR . 'purchase-' . $purchase->id . '-' . uniqid() . '.html';
        $pdfPath = $htmlDirectory . DIRECTORY_SEPARATOR . 'purchase-' . $purchase->id . '-' . uniqid() . '.pdf';

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

        return response()->download(
            $pdfPath,
            'purchase-' . ($purchase->bill_number ?: $purchase->id) . '.pdf'
        )->deleteFileAfterSend(true);
    }

    private function buildPurchaseInvoiceViewData(Purchase $purchase, array $overrides = []): array
    {
        $primaryPayment = $purchase->payments->first();
        $invoicePreviewData = [
            'title' => 'Purchase Bill',
            'businessName' => (string) config('app.name', 'My Company'),
            'phone' => (string) ($purchase->phone ?: ($purchase->party?->phone ?: '')),
            'invoiceNo' => (string) ($purchase->bill_number ?: $purchase->id),
            'date' => optional($purchase->bill_date)->format('d/m/Y') ?: now()->format('d/m/Y'),
            'time' => optional($purchase->created_at)->format('h:i A') ?: now()->format('h:i A'),
            'dueDate' => optional($purchase->bill_date)->format('d/m/Y') ?: now()->format('d/m/Y'),
            'billTo' => (string) ($purchase->party_name ?: ($purchase->party?->name ?: 'Supplier')),
            'billAddress' => (string) ($purchase->billing_address ?: ''),
            'billPhone' => (string) ($purchase->phone ?: ($purchase->party?->phone ?: '')),
            'shipTo' => (string) ($purchase->billing_address ?: ''),
            'description' => (string) ($purchase->description ?: 'Thanks for doing business with us!'),
            'subtotal' => (float) ($purchase->total_amount ?? 0),
            'discount' => (float) ($purchase->discount_rs ?? 0),
            'taxAmount' => (float) ($purchase->tax_amount ?? 0),
            'total' => (float) ($purchase->grand_total ?? 0),
            'received' => (float) ($purchase->paid_amount ?? 0),
            'balance' => (float) ($purchase->balance ?? 0),
            'items' => $purchase->items->map(function ($item) use ($purchase) {
                return [
                    'name' => (string) ($item->item_name ?: 'Item'),
                    'hsn' => (string) ($item->item_code ?: ''),
                    'qty' => (string) ($item->quantity ?? 0),
                    'unit' => (string) ($item->unit ?: ''),
                    'rate' => (float) ($item->unit_price ?? 0),
                    'disc' => number_format((float) ($item->discount ?? 0), 2, '.', ''),
                    'gst' => rtrim(rtrim(number_format((float) ($purchase->tax_pct ?? 0), 2, '.', ''), '0'), '.') . '%',
                    'amt' => (float) ($item->amount ?? 0),
                ];
            })->values()->all(),
            'bankName' => (string) ($primaryPayment?->bankAccount?->bank_name ?: $primaryPayment?->bankAccount?->display_name ?: $primaryPayment?->payment_type ?: ''),
            'bankAccountNumber' => (string) ($primaryPayment?->bankAccount?->account_number ?: ''),
            'bankAccountHolder' => (string) ($primaryPayment?->bankAccount?->account_holder_name ?: ''),
        ];

        return array_merge([
            'purchase' => $purchase,
            'invoicePreviewData' => $invoicePreviewData,
            'pageTitle' => 'Purchase Preview',
            'browserTabLabel' => 'Purchase Preview',
            'saveCloseUrl' => route('purchase-expenses'),
            'initialMode' => (string) request()->query('theme', 'tally'),
            'initialRegularThemeId' => (int) request()->query('theme_id', 1),
            'initialThermalThemeId' => (int) request()->query('theme_id', 1),
            'initialAccent' => (string) request()->query('color', '#707070'),
            'initialAccent2' => (string) request()->query('color2', '#ff981f'),
            'reactCss' => asset('react-invoice/assets/index-7A0P_pSc.css'),
            'reactJs' => asset('react-invoice/assets/index-B2etBuUm.js'),
        ], $overrides);
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

    private function validatePurchase(Request $request): array
    {
        return $request->validate([
            'source_purchase_order_id' => 'nullable|exists:purchases,id',
            'party_id' => 'nullable|exists:parties,id',
            'party_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'billing_address' => 'nullable|string|max:1000',
            'bill_number' => 'nullable|string|max:100',
            'bill_date' => 'nullable|date',
            'total_qty' => 'nullable|integer|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'discount_pct' => 'nullable|numeric|min:0',
            'discount_rs' => 'nullable|numeric|min:0',
            'tax_pct' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'shipping_charge' => 'nullable|numeric|min:0',
            'round_off' => 'nullable|numeric',
            'grand_total' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'balance' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'image_path' => 'nullable|string|max:255',
            'document_path' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'nullable|exists:items,id',
            'items.*.item_name' => 'nullable|string|max:255',
            'items.*.item_category' => 'nullable|string|max:255',
            'items.*.item_code' => 'nullable|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'nullable|integer|min:0',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.amount' => 'nullable|numeric|min:0',
            'payments' => 'nullable|array',
            'payments.*.payment_type' => 'required|string|max:50',
            'payments.*.bank_account_id' => 'nullable|exists:bank_accounts,id',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.reference' => 'nullable|string|max:255',
        ]);
    }

    private function savePurchase(Purchase $purchase, array $data): Purchase
    {
        return DB::transaction(function () use ($purchase, $data) {
            if (!empty($data['source_purchase_order_id'])) {
                $existingConvertedPurchase = Purchase::where('type', 'purchase_bill')
                    ->where('source_purchase_order_id', $data['source_purchase_order_id'])
                    ->when($purchase->exists, function ($query) use ($purchase) {
                        $query->where('id', '!=', $purchase->id);
                    })
                    ->first();

                if ($existingConvertedPurchase) {
                    throw ValidationException::withMessages([
                        'source_purchase_order_id' => 'This purchase order is already converted to purchase #' . ($existingConvertedPurchase->bill_number ?? $existingConvertedPurchase->id),
                    ]);
                }
            }

            $purchase->fill([
                'type' => 'purchase_bill',
                'source_purchase_order_id' => $data['source_purchase_order_id'] ?? null,
                'party_id' => $data['party_id'] ?? null,
                'party_name' => $data['party_name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'billing_address' => $data['billing_address'] ?? null,
                'bill_number' => $data['bill_number'] ?? null,
                'bill_date' => $data['bill_date'] ?? now()->toDateString(),
                'total_qty' => $data['total_qty'] ?? 0,
                'total_amount' => $data['total_amount'] ?? 0,
                'discount_pct' => $data['discount_pct'] ?? 0,
                'discount_rs' => $data['discount_rs'] ?? 0,
                'tax_pct' => $data['tax_pct'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'shipping_charge' => $data['shipping_charge'] ?? 0,
                'round_off' => $data['round_off'] ?? 0,
                'grand_total' => $data['grand_total'] ?? 0,
                'paid_amount' => $data['paid_amount'] ?? 0,
                'balance' => $data['balance'] ?? 0,
                'description' => $data['description'] ?? null,
                'image_path' => $data['image_path'] ?? null,
                'document_path' => $data['document_path'] ?? null,
            ]);

            $purchase->save();

            if (empty($purchase->bill_number)) {
                $purchase->bill_number = TransactionNumberPrefix::format('purchase_bill', $purchase->id);
                $purchase->save();
            }

            $purchase->items()->delete();
            foreach ($data['items'] as $item) {
                $purchase->items()->create([
                    'item_id' => $item['item_id'] ?? null,
                    'item_name' => $item['item_name'] ?? null,
                    'item_category' => $item['item_category'] ?? null,
                    'item_code' => $item['item_code'] ?? null,
                    'item_description' => $item['item_description'] ?? null,
                    'quantity' => $item['quantity'] ?? 0,
                    'unit' => $item['unit'] ?? null,
                    'unit_price' => $item['unit_price'] ?? 0,
                    'discount' => $item['discount'] ?? 0,
                    'amount' => $item['amount'] ?? 0,
                ]);
            }

            $purchase->payments()->delete();
            foreach ($data['payments'] ?? [] as $payment) {
                $purchase->payments()->create([
                    'payment_type' => $payment['payment_type'],
                    'bank_account_id' => $payment['bank_account_id'] ?? null,
                    'amount' => $payment['amount'] ?? 0,
                    'reference' => $payment['reference'] ?? null,
                ]);
            }

            return $purchase->fresh(['items', 'payments.bankAccount', 'party']);
        });
    }
}
