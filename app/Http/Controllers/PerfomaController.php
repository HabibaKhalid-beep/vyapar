<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Party;
use App\Models\Sale;
use Illuminate\Http\Request;

class PerfomaController extends Controller
{
    public function proformaInvoice(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $baseQuery = Sale::with(['items', 'payments', 'party'])
            ->where('type', 'proforma')
            ->orderByDesc('created_at');

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
            'convertedSales',
            'convertedSaleOrders'
        ));
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
        $data = $this->validateProformaRequest($request);

        $sale = Sale::create($this->buildSalePayload($data));
        $this->syncItems($sale, $data['items']);

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'bill_number' => $sale->bill_number,
            'redirect_url' => route('proforma-invoice'),
        ]);
    }

    public function update(Request $request, Sale $sale)
    {
        abort_unless($sale->type === 'proforma', 404);

        $data = $this->validateProformaRequest($request);

        $sale->update($this->buildSalePayload($data));
        $sale->items()->delete();
        $this->syncItems($sale, $data['items']);

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'bill_number' => $sale->bill_number,
            'redirect_url' => route('proforma-invoice'),
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
        $nextInvoiceNumber = 'PI-' . str_pad((string) $nextSaleId, 4, '0', STR_PAD_LEFT);

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

    private function buildSalePayload(array $data): array
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
            'image_path' => $data['image_path'] ?? null,
        ];
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
