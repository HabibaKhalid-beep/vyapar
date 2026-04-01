<?php

namespace App\Http\Controllers;

use App\Models\ChallanDetail;
use App\Models\Item;
use App\Models\Party;
use App\Models\Sale;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function deliveryChallan()
    {
        $challans = Sale::where('type', 'delivery_challan')
            ->with(['items', 'challanDetail', 'party'])
            ->orderByDesc('created_at')
            ->get();

        $convertedInvoices = Sale::where('type', 'invoice')
            ->whereNotNull('reference_id')
            ->get()
            ->keyBy('reference_id');

        return view('dashboard.delivery.delivery-challan', compact('challans', 'convertedInvoices'));
    }

    public function createChallan()
    {
        return $this->renderChallanForm();
    }

    public function edit(Sale $sale)
    {
        abort_unless($sale->type === 'delivery_challan', 404);

        $sale->load(['items', 'challanDetail']);

        return $this->renderChallanForm($sale);
    }

    public function duplicate(Sale $sale)
    {
        abort_unless($sale->type === 'delivery_challan', 404);

        $sale->load(['items', 'challanDetail']);

        return $this->renderChallanForm(null, $sale);
    }

    private function renderChallanForm(?Sale $challan = null, ?Sale $duplicateChallan = null)
    {
        $items = Item::orderBy('name')->get();
        $parties = Party::orderBy('name')->get();
        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = 'DC-' . str_pad((string) $nextSaleId, 4, '0', STR_PAD_LEFT);

        return view('dashboard.delivery.create-challan', compact('items', 'parties', 'nextInvoiceNumber', 'challan', 'duplicateChallan'));
    }

    public function store(Request $request)
    {
        $data = $this->validateChallanRequest($request);

        $sale = Sale::create($this->buildSalePayload($data));

        foreach ($data['items'] as $item) {
            $sale->items()->create($this->buildItemPayload($item));
        }

        ChallanDetail::create([
            'sale_id' => $sale->id,
            'challan_number' => $sale->bill_number,
            'invoice_date' => $sale->invoice_date,
            'due_date' => $sale->due_date,
        ]);

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'bill_number' => $sale->bill_number,
            'redirect_url' => route('delivery-challan'),
        ]);
    }

    public function update(Request $request, Sale $sale)
    {
        abort_unless($sale->type === 'delivery_challan', 404);

        $data = $this->validateChallanRequest($request);

        $sale->update($this->buildSalePayload($data));
        $sale->items()->delete();

        foreach ($data['items'] as $item) {
            $sale->items()->create($this->buildItemPayload($item));
        }

        $sale->challanDetail()->updateOrCreate(
            ['sale_id' => $sale->id],
            [
                'challan_number' => $sale->bill_number,
                'invoice_date' => $sale->invoice_date,
                'due_date' => $sale->due_date,
            ]
        );

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'bill_number' => $sale->bill_number,
            'redirect_url' => route('delivery-challan'),
        ]);
    }

    public function destroy(Sale $sale)
    {
        abort_unless($sale->type === 'delivery_challan', 404);

        $sale->challanDetail()?->delete();
        $sale->items()->delete();
        $sale->payments()->delete();
        $sale->delete();

        return response()->json([
            'success' => true,
            'message' => 'Delivery challan deleted successfully.',
        ]);
    }

    public function preview(Sale $sale)
    {
        abort_unless($sale->type === 'delivery_challan', 404);
        $sale->load(['items', 'challanDetail']);

        return view('dashboard.delivery.challan-preview', compact('sale'));
    }

    public function print(Sale $sale)
    {
        abort_unless($sale->type === 'delivery_challan', 404);
        $sale->load(['items', 'challanDetail']);

        return view('dashboard.delivery.challan-preview', ['sale' => $sale, 'autoPrint' => true]);
    }

    public function pdf(Sale $sale)
    {
        abort_unless($sale->type === 'delivery_challan', 404);
        $sale->load(['items', 'challanDetail']);

        return view('dashboard.delivery.challan-preview', ['sale' => $sale, 'pdfMode' => true]);
    }

    private function validateChallanRequest(Request $request): array
    {
        return $request->validate([
            'party_id' => 'nullable|exists:parties,id',
            'phone' => 'nullable|string|max:50',
            'billing_address' => 'nullable|string|max:1000',
            'shipping_address' => 'nullable|string|max:1000',
            'bill_number' => 'required|string|max:100',
            'invoice_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'total_qty' => 'nullable|integer|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'discount_pct' => 'nullable|numeric|min:0',
            'discount_rs' => 'nullable|numeric|min:0',
            'tax_pct' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'round_off' => 'nullable|numeric',
            'grand_total' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'image_path' => 'nullable|string|max:255',
            'document_path' => 'nullable|string|max:255',
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
            'type' => 'delivery_challan',
            'party_id' => $data['party_id'] ?? null,
            'phone' => $data['phone'] ?? null,
            'billing_address' => $data['billing_address'] ?? null,
            'shipping_address' => $data['shipping_address'] ?? null,
            'bill_number' => $data['bill_number'],
            'invoice_date' => $data['invoice_date'] ?? now()->toDateString(),
            'due_date' => $data['due_date'] ?? ($data['invoice_date'] ?? now()->toDateString()),
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
            'status' => $data['status'] ?? 'open',
            'description' => $data['description'] ?? null,
            'image_path' => $data['image_path'] ?? null,
            'document_path' => $data['document_path'] ?? null,
        ];
    }

    private function buildItemPayload(array $item): array
    {
        return [
            'item_name' => $item['item_name'] ?? null,
            'item_category' => $item['item_category'] ?? null,
            'item_code' => $item['item_code'] ?? null,
            'item_description' => $item['item_description'] ?? null,
            'quantity' => $item['quantity'] ?? 0,
            'unit' => $item['unit'] ?? null,
            'unit_price' => $item['unit_price'] ?? 0,
            'discount' => $item['discount'] ?? 0,
            'amount' => $item['amount'] ?? 0,
        ];
    }
}
