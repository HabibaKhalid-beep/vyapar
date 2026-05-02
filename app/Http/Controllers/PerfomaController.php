<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Party;
use App\Models\Sale;
use App\Support\TransactionNumberPrefix;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class PerfomaController extends Controller
{
    public function proformaInvoice(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $dateRange = $request->get('date_range', 'all');
        $partyId = $request->get('party_id', 'all');
        $dateRangeLabel = $this->formatProformaDateRangeText($dateRange);

        $baseQuery = Sale::with(['items', 'payments', 'party'])
            ->where('type', 'proforma')
            ->orderByDesc('created_at');

        if ($dateRange !== 'all') {
            $range = $this->resolveProformaDateRange($dateRange);
            if ($range) {
                $baseQuery->whereBetween('invoice_date', [$range['from']->toDateString(), $range['to']->toDateString()]);
            }
        }

        if ($partyId !== 'all') {
            $baseQuery->where('party_id', $partyId);
        }

        $allProformas = (clone $baseQuery)->get();

        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('bill_number', 'like', "%{$search}%")
                    ->orWhereHas('party', function ($partyQuery) use ($search) {
                        $partyQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $proformas = $baseQuery->get();

        $partyOptions = Party::whereIn('id', $allProformas->pluck('party_id')->filter()->unique())
            ->orderBy('name')
            ->get();

        $convertedSales = Sale::where('type', 'invoice')
            ->whereNotNull('reference_id')
            ->whereIn('reference_id', $allProformas->pluck('id'))
            ->pluck('bill_number', 'reference_id');

        $convertedSaleOrders = Sale::where('type', 'sale_order')
            ->whereNotNull('reference_id')
            ->whereIn('reference_id', $allProformas->pluck('id'))
            ->pluck('bill_number', 'reference_id');

        return view('dashboard.perfoma.perfoma-invoice', compact(
            'proformas',
            'allProformas',
            'search',
            'dateRange',
            'dateRangeLabel',
            'partyId',
            'partyOptions',
            'convertedSales',
            'convertedSaleOrders'
        ));
    }

    private function resolveProformaDateRange(string $dateRange): ?array
    {
        $today = Carbon::today();

        switch ($dateRange) {
            case 'this_month':
                return ['from' => $today->copy()->startOfMonth(), 'to' => $today->copy()->endOfMonth()];
            case 'last_month':
                $previous = $today->copy()->subMonthNoOverflow();
                return ['from' => $previous->copy()->startOfMonth(), 'to' => $previous->copy()->endOfMonth()];
            case 'this_quarter':
                return ['from' => $today->copy()->startOfQuarter(), 'to' => $today->copy()->endOfQuarter()];
            case 'this_year':
                return ['from' => $today->copy()->startOfYear(), 'to' => $today->copy()->endOfYear()];
            default:
                return null;
        }
    }

    private function formatProformaDateRangeText(string $dateRange): string
    {
        $range = $this->resolveProformaDateRange($dateRange);
        if (! $range) {
            return 'All dates';
        }

        return $range['from']->format('d/m/Y') . ' To ' . $range['to']->format('d/m/Y');
    }

    public function createProformaInvoice()
    {
        return $this->renderProformaForm();
    }

    public function edit(Sale $sale)
    {
        abort_unless($sale->type === 'proforma', 404);

        $sale->load(['items', 'party']);

        return $this->renderProformaForm($sale);
    }

    public function duplicate(Sale $sale)
    {
        abort_unless($sale->type === 'proforma', 404);

        $sale->load(['items', 'party']);

        return $this->renderProformaForm(null, $sale);
    }

    public function store(Request $request)
    {
        $this->normalizeJsonInputs($request);
        $data = $this->validateProformaRequest($request);
        $uploadedImagePaths = $this->storeAttachmentFiles($request->file('images', []), 'proforma/images');
        $uploadedDocumentPaths = $this->storeAttachmentFiles($request->file('documents', []), 'proforma/documents');

        $sale = Sale::create($this->buildSalePayload($data, $uploadedImagePaths, $uploadedDocumentPaths));
        $this->syncItems($sale, $data['items']);

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'bill_number' => $sale->bill_number,
            'redirect_url' => route('proforma-invoice.react', $sale),
            'share_url' => route('proforma-invoice.react', $sale),
        ]);
    }

    public function update(Request $request, Sale $sale)
    {
        abort_unless($sale->type === 'proforma', 404);

        $this->normalizeJsonInputs($request);
        $data = $this->validateProformaRequest($request);
        $existingImagePaths = array_values(array_filter(array_merge($sale->image_paths ?? [], $data['image_paths'] ?? [])));
        $existingDocumentPaths = array_values(array_filter(array_merge($sale->document_paths ?? [], $data['document_paths'] ?? [])));
        $uploadedImagePaths = $this->storeAttachmentFiles($request->file('images', []), 'proforma/images');
        $uploadedDocumentPaths = $this->storeAttachmentFiles($request->file('documents', []), 'proforma/documents');

        $sale->update($this->buildSalePayload(
            $data,
            array_values(array_filter(array_merge($existingImagePaths, $uploadedImagePaths))),
            array_values(array_filter(array_merge($existingDocumentPaths, $uploadedDocumentPaths)))
        ));
        $sale->items()->delete();
        $this->syncItems($sale, $data['items']);

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'bill_number' => $sale->bill_number,
            'redirect_url' => route('proforma-invoice.react', $sale),
            'share_url' => route('proforma-invoice.react', $sale),
        ]);
    }

    public function destroy(Sale $sale)
    {
        abort_unless($sale->type === 'proforma', 404);

        $sale->items()->delete();
        $sale->payments()->delete();
        $sale->delete();

        return response()->json([
            'success' => true,
            'message' => 'Proforma invoice deleted successfully.',
        ]);
    }

    public function preview(Sale $sale)
    {
        abort_unless($sale->type === 'proforma', 404);
        $sale->load(['items', 'party']);

        return view('dashboard.perfoma.proforma-preview', compact('sale'));
    }

    public function print(Sale $sale)
    {
        abort_unless($sale->type === 'proforma', 404);
        $sale->load(['items', 'party']);

        return view('dashboard.perfoma.proforma-preview', ['sale' => $sale, 'autoPrint' => true]);
    }

    public function pdf(Sale $sale)
    {
        abort_unless($sale->type === 'proforma', 404);
        $sale->load(['items', 'party']);

        return view('dashboard.perfoma.proforma-preview', ['sale' => $sale, 'pdfMode' => true]);
    }

    private function renderProformaForm(?Sale $proforma = null, ?Sale $duplicateProforma = null)
    {
        $items = Item::active()->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();
        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = TransactionNumberPrefix::format('proforma_invoice', $nextSaleId);

        return view('dashboard.perfoma.create_proforma_invoice', compact(
            'items',
            'parties',
            'nextInvoiceNumber',
            'proforma',
            'duplicateProforma'
        ));
    }

    private function validateProformaRequest(Request $request): array
    {
        return $request->validate([
            'party_id' => 'nullable|exists:parties,id',
            'phone' => 'nullable|string|max:50',
            'billing_address' => 'nullable|string|max:1000',
            'bill_number' => 'required|string|max:100',
            'invoice_date' => 'nullable|date',
            'total_qty' => 'nullable|integer|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'discount_pct' => 'nullable|numeric|min:0',
            'discount_rs' => 'nullable|numeric|min:0',
            'tax_pct' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'round_off' => 'nullable|numeric',
            'grand_total' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'image_path' => 'nullable|string|max:255',
            'document_path' => 'nullable|string|max:255',
            'image_paths' => 'nullable|array',
            'image_paths.*' => 'nullable|string|max:255',
            'document_paths' => 'nullable|array',
            'document_paths.*' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx',
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|max:5120|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'nullable|string|max:255',
            'items.*.item_category' => 'nullable|string|max:255',
            'items.*.item_code' => 'nullable|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'nullable|integer|min:0',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.amount' => 'nullable|numeric|min:0',
        ]);
    }

    private function buildSalePayload(array $data, array $imagePaths = [], array $documentPaths = []): array
    {
        return [
            'type' => 'proforma',
            'party_id' => $data['party_id'] ?? null,
            'phone' => $data['phone'] ?? null,
            'billing_address' => $data['billing_address'] ?? null,
            'bill_number' => $data['bill_number'],
            'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
            'total_qty' => $data['total_qty'] ?? 0,
            'total_amount' => $data['total_amount'] ?? 0,
            'discount_pct' => $data['discount_pct'] ?? 0,
            'discount_rs' => $data['discount_rs'] ?? 0,
            'tax_pct' => $data['tax_pct'] ?? 0,
            'tax_amount' => $data['tax_amount'] ?? 0,
            'round_off' => $data['round_off'] ?? 0,
            'grand_total' => $data['grand_total'] ?? 0,
            'received_amount' => 0,
            'balance' => $data['grand_total'] ?? 0,
            'status' => 'open',
            'description' => $data['description'] ?? null,
            'image_path' => $imagePaths[0] ?? ($data['image_path'] ?? null),
            'document_path' => $documentPaths[0] ?? ($data['document_path'] ?? null),
            'image_paths' => !empty($imagePaths) ? array_values($imagePaths) : null,
            'document_paths' => !empty($documentPaths) ? array_values($documentPaths) : null,
        ];
    }

    private function normalizeJsonInputs(Request $request): void
    {
        foreach (['items', 'image_paths', 'document_paths'] as $field) {
            $value = $request->input($field);
            if (! is_string($value)) {
                continue;
            }

            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge([$field => $decoded]);
            }
        }
    }

    /**
     * @param  array<int, UploadedFile>|UploadedFile|null  $files
     * @return array<int, string>
     */
    private function storeAttachmentFiles($files, string $directory): array
    {
        $storedPaths = [];

        foreach ((array) $files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $storedPaths[] = $file->store($directory, 'public');
        }

        return $storedPaths;
    }

    private function syncItems(Sale $sale, array $items): void
    {
        foreach ($items as $item) {
            $sale->items()->create([
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
    }
}
