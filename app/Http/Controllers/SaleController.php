<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Item;
use App\Models\Party;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SaleController extends Controller
{
    public function index()
    {
        $sales = Sale::with(['payments', 'bankAccount', 'party'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.sales.sale_index', compact('sales'));
    }

    public function create(string $type = 'invoice')
    {
        $bankAccounts = BankAccount::orderBy('display_name')->get();
        $items = Item::orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $prefixes = [
            'invoice' => '',
            'estimate' => 'EST-',
            'sale_order' => 'SO-',
            'proforma' => 'PI-',
            'delivery_challan' => 'DC-',
            'sale_return' => 'SR-',
            'pos' => 'POS-',
        ];
        $nextInvoiceNumber = ($prefixes[$type] ?? '') . $nextSaleId;

        return view('dashboard.sales.create', compact('bankAccounts', 'items', 'parties', 'nextInvoiceNumber', 'type'));
    }

    public function createFromEstimate(Sale $sale)
    {
        if ($sale->type !== 'estimate') {
            abort(404);
        }

        if ($sale->status === 'converted') {
            return redirect()
                ->route('sale.estimate')
                ->with('error', 'This estimate is already converted to sale invoice #' . ($sale->reference_id ?? ''));
        }

        $bankAccounts = BankAccount::orderBy('display_name')->get();
        $items = Item::orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        $sale->load(['items']);

        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = (string) $nextSaleId;
        $convertedSaleData = $this->mapEstimateToSaleDraft($sale, $nextInvoiceNumber);
        $type = 'invoice';

        return view('dashboard.sales.create', compact(
            'bankAccounts',
            'items',
            'parties',
            'nextInvoiceNumber',
            'convertedSaleData',
            'type'
        ));
    }

    public function createFromSaleOrder(Sale $sale)
    {
        if ($sale->type !== 'sale_order') {
            abort(404);
        }

        if ($sale->status === 'completed') {
            return redirect()
                ->route('sale-order')
                ->with('error', 'This sale order is already converted to invoice #' . ($sale->reference_id ?? ''));
        }

        $bankAccounts = BankAccount::orderBy('display_name')->get();
        $items = Item::orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        $sale->load(['items']);

        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = (string) $nextSaleId;
        $convertedSaleData = $this->mapSaleOrderToSaleDraft($sale, $nextInvoiceNumber);
        $type = 'invoice';

        return view('dashboard.sales.create', compact(
            'bankAccounts',
            'items',
            'parties',
            'nextInvoiceNumber',
            'convertedSaleData',
            'type'
        ));
    }

    public function createFromDeliveryChallan(Sale $sale)
    {
        if ($sale->type !== 'delivery_challan') {
            abort(404);
        }

        if ($sale->status === 'closed') {
            return redirect()
                ->route('delivery-challan')
                ->with('error', 'This delivery challan is already converted to invoice #' . ($sale->reference_id ?? ''));
        }

        $bankAccounts = BankAccount::orderBy('display_name')->get();
        $items = Item::orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        $sale->load(['items']);

        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = (string) $nextSaleId;
        $convertedSaleData = $this->mapDeliveryChallanToSaleDraft($sale, $nextInvoiceNumber);
        $type = 'invoice';

        return view('dashboard.sales.create', compact(
            'bankAccounts',
            'items',
            'parties',
            'nextInvoiceNumber',
            'convertedSaleData',
            'type'
        ));
    }
public function createFromProforma(Sale $sale)
    {
        if ($sale->type !== 'proforma') {
            abort(404);
        }

        if ($sale->status === 'converted') {
            return redirect()
                ->route('proforma-invoice')
                ->with('error', 'This proforma is already converted to sale invoice #' . ($sale->reference_id ?? ''));
        }

        $bankAccounts = BankAccount::orderBy('display_name')->get();
        $items = Item::orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        $sale->load(['items']);

        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = (string) $nextSaleId;
        $convertedSaleData = $this->mapProformaToSaleDraft($sale, $nextInvoiceNumber);
        $type = 'invoice';

        return view('dashboard.sales.create', compact(
            'bankAccounts',
            'items',
            'parties',
            'nextInvoiceNumber',
            'convertedSaleData',
            'type'
        ));
    }

    // ── POS METHODS ─────────────────────────────────────────────────────────────
    // Both pos() and pos1() share the same helper so data is consistent.

    /**
     * Shared data loader for all POS views.
     * Passes:
     *   $items        – all product-type items (for the search/scan bar)
     *   $parties      – all parties/customers (for the customer dropdown)
     *   $paymentModes – available payment method names (for the Payment Mode select)
     */
    private function posData(): array
    {
        $items = Item::where('type', 'product')
            ->orderBy('name')
            ->get([
                'id', 'name', 'item_code', 'unit',
                'sale_price', 'purchase_price', 'opening_qty',
            ]);

        $parties = Party::orderBy('name')
            ->get(['id', 'name', 'phone']);

        // If you have a PaymentMode model, replace the array below with:
        // $paymentModes = \App\Models\PaymentMode::orderBy('sort_order')->pluck('name')->toArray();
        $paymentModes = ['Cash', 'Card', 'UPI', 'HBL', 'Credit'];

        return compact('items', 'parties', 'paymentModes');
    }

    /**
     * Primary POS route (used by the tab-based POS blade).
     */
    public function pos()
    {
        return view('dashboard.sales.pos', $this->posData());
    }

    /**
     * Alias / legacy POS route – identical data, same view.
     */
    public function pos1()
    {
        return view('dashboard.sales.pos', $this->posData());
    }
    // ────────────────────────────────────────────────────────────────────────────

    public function edit(Sale $sale)
    {
        $bankAccounts = BankAccount::orderBy('display_name')->get();
        $items = Item::orderBy('name')->get();
        $parties = Party::orderBy('name')->get();
        $type = $sale->type ?? 'invoice';

        $sale->load(['items', 'payments', 'party']);

        // Provide full URLs for existing image/document if stored in the public disk
        if ($sale->image_path) {
            $sale->image_url = Storage::disk('public')->url($sale->image_path);
        }
        if ($sale->document_path) {
            $sale->document_url = Storage::disk('public')->url($sale->document_path);
        }

        return view('dashboard.sales.create', compact('bankAccounts', 'items', 'parties', 'sale', 'type'));
    }

    public function update(Request $request, Sale $sale)
    {
        // Validate incoming data (same as store)
        $data = $request->validate([
            'type' => 'nullable|in:invoice,estimate,sale_order,proforma,delivery_challan,sale_return,pos',
            'source_estimate_id' => 'nullable|exists:sales,id',
            'source_sale_order_id' => 'nullable|exists:sales,id',
            'source_challan_id' => 'nullable|exists:sales,id',
            'source_proforma_id' => 'nullable|exists:sales,id',
            'party_id' => 'nullable|exists:parties,id',
            'phone' => 'nullable|string|max:50',
            'billing_address' => 'nullable|string|max:1000',
            'shipping_address' => 'nullable|string|max:1000',
            'bill_number' => 'nullable|string|max:100',
            'invoice_date' => 'nullable|date',
            'order_date' => 'nullable|date',
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
            'payments' => 'nullable|array',
            'payments.*.payment_type' => 'required|string|max:50',
            'payments.*.bank_account_id' => 'nullable|exists:bank_accounts,id',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.reference' => 'nullable|string|max:255',
        ]);

        // When updating, treat payment entries as additional payments (keep previous received amount)
        $existingReceived = floatval($sale->received_amount ?? 0);
        $receivedAmount = $existingReceived;
        if (!empty($data['payments']) && is_array($data['payments'])) {
            foreach ($data['payments'] as $payment) {
                if (!empty($payment['bank_account_id'])) {
                    $receivedAmount += floatval($payment['amount'] ?? 0);
                }
            }
        }

        $type = $data['type'] ?? $sale->type ?? 'invoice';
        $grandTotal = floatval($data['grand_total'] ?? 0);
        $balance = max(0, $grandTotal - $receivedAmount);
        $status = $this->resolveStatusForType(
            $type,
            $receivedAmount,
            $grandTotal,
            $data['status'] ?? null,
            $sale->status
        );

        $sale->update([
            'type' => $type,
            'party_id' => $data['party_id'] ?? $sale->party_id,
            'phone' => $data['phone'] ?? null,
            'billing_address' => $data['billing_address'] ?? null,
            'shipping_address' => $data['shipping_address'] ?? null,
            'bill_number' => $data['bill_number'] ?? $sale->bill_number,
            'invoice_date' => $data['invoice_date'] ?? $sale->invoice_date,
            'order_date' => $data['order_date'] ?? $sale->order_date,
            'due_date' => $data['due_date'] ?? $sale->due_date,
            'total_qty' => $data['total_qty'] ?? 0,
            'total_amount' => $data['total_amount'] ?? 0,
            'discount_pct' => $data['discount_pct'] ?? 0,
            'discount_rs' => $data['discount_rs'] ?? 0,
            'tax_pct' => $data['tax_pct'] ?? 0,
            'tax_amount' => $data['tax_amount'] ?? 0,
            'round_off' => $data['round_off'] ?? 0,
            'grand_total' => $grandTotal,
            'received_amount' => $receivedAmount,
            'balance' => $balance,
            'status' => $status,
            'description' => $data['description'] ?? null,
            'image_path' => $data['image_path'] ?? null,
            'document_path' => $data['document_path'] ?? null,
        ]);

        // Replace items (keep payment history intact for edit mode)
        $sale->items()->delete();
        foreach ($data['items'] as $item) {
            $itemRecord = null;
            if (!empty($item['item_name'])) {
                $itemRecord = Item::whereRaw('LOWER(TRIM(name)) = ?', [
                    strtolower(trim($item['item_name']))
                ])->first();
            }
            $sale->items()->create([
                'item_id'          => $itemRecord?->id,
                'item_name'        => $item['item_name']        ?? null,
                'item_category'    => $item['item_category']    ?? null,
                'item_code'        => $item['item_code']        ?? null,
                'item_description' => $item['item_description'] ?? null,
                'quantity'         => $item['quantity']         ?? 0,
                'unit'             => $item['unit']             ?? null,
                'unit_price'       => $item['unit_price']       ?? 0,
                'discount'         => $item['discount']         ?? 0,
                'amount'           => $item['amount']           ?? 0,
            ]);
        }

        // Append new payments (maintain payment history; edit mode treats payments as additional amounts)
        if (!empty($data['payments']) && is_array($data['payments'])) {
            foreach ($data['payments'] as $payment) {
                // Skip empty or incomplete payment entries
                if (empty($payment['bank_account_id']) || empty($payment['amount'])) {
                    continue;
                }

                $sale->payments()->create([
                    'payment_type' => $payment['payment_type'],
                    'bank_account_id' => $payment['bank_account_id'] ?? null,
                    'amount' => $payment['amount'],
                    'reference' => $payment['reference'] ?? null,
                ]);

                if (!empty($payment['bank_account_id']) && !empty($payment['amount'])) {
                    $bank = BankAccount::find($payment['bank_account_id']);
                    if ($bank) {
                        $bank->opening_balance = ($bank->opening_balance ?? 0) + floatval($payment['amount']);
                        $bank->save();
                    }
                }
            }
        }

        $redirectUrl = match ($sale->type) {
            'estimate' => route('sale.estimate'),
            'sale_order' => route('sale-order'),
            default => route('sale.index'),
        };

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'bill_number' => $sale->bill_number,
            'redirect_url' => $redirectUrl,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'nullable|in:invoice,estimate,sale_order,proforma,delivery_challan,sale_return,pos',
            'source_estimate_id' => 'nullable|exists:sales,id',
            'source_sale_order_id' => 'nullable|exists:sales,id',
            'source_challan_id' => 'nullable|exists:sales,id',
            'source_proforma_id' => 'nullable|exists:sales,id',
            'party_id' => 'nullable|exists:parties,id',
            'phone' => 'nullable|string|max:50',
            'billing_address' => 'nullable|string|max:1000',
            'shipping_address' => 'nullable|string|max:1000',
            'bill_number' => 'nullable|string|max:100',
            'invoice_date' => 'nullable|date',
            'order_date' => 'nullable|date',
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
            'payments' => 'nullable|array',
            'payments.*.payment_type' => 'required|string|max:50',
            'payments.*.bank_account_id' => 'nullable|exists:bank_accounts,id',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.reference' => 'nullable|string|max:255',
        ]);

        $receivedAmount = 0;

        // Calculate received amount from payments (only bank payments count as received)
        if (!empty($data['payments']) && is_array($data['payments'])) {
            foreach ($data['payments'] as $payment) {
                if (!empty($payment['bank_account_id'])) {
                    $receivedAmount += floatval($payment['amount'] ?? 0);
                }
            }
        }

        $type = $data['type'] ?? 'invoice';
        $grandTotal = floatval($data['grand_total'] ?? 0);
        $balance = max(0, $grandTotal - $receivedAmount);
        $status = $this->resolveStatusForType(
            $type,
            $receivedAmount,
            $grandTotal,
            $data['status'] ?? null
        );

        $sale = Sale::create([
            'type' => $type,
            'party_id' => $data['party_id'] ?? null,
            'phone' => $data['phone'] ?? null,
            'billing_address' => $data['billing_address'] ?? null,
            'shipping_address' => $data['shipping_address'] ?? null,
            'bill_number' => $data['bill_number'] ?? null,
            'invoice_date' => $data['invoice_date'] ?? ($type === 'sale_order' ? null : now()),
            'order_date' => $data['order_date'] ?? null,
            'due_date' => $data['due_date'] ?? null,
            'total_qty' => $data['total_qty'] ?? 0,
            'total_amount' => $data['total_amount'] ?? 0,
            'discount_pct' => $data['discount_pct'] ?? 0,
            'discount_rs' => $data['discount_rs'] ?? 0,
            'tax_pct' => $data['tax_pct'] ?? 0,
            'tax_amount' => $data['tax_amount'] ?? 0,
            'round_off' => $data['round_off'] ?? 0,
            'grand_total' => $grandTotal,
            'received_amount' => $receivedAmount,
            'balance' => $balance,
            'status' => $status,
            'description' => $data['description'] ?? null,
            'image_path' => $data['image_path'] ?? null,
            'document_path' => $data['document_path'] ?? null,
        ]);

        // Auto-generate invoice number based on the sale ID if not provided
        if (empty($sale->bill_number)) {
            $sale->bill_number = (string) $sale->id;
            $sale->save();
        }

        foreach ($data['items'] as $item) {
            $itemRecord = null;
            if (!empty($item['item_name'])) {
                $itemRecord = Item::whereRaw('LOWER(TRIM(name)) = ?', [
                    strtolower(trim($item['item_name']))
                ])->first();
            }
            $sale->items()->create([
                'item_id'          => $itemRecord?->id,
                'item_name'        => $item['item_name']        ?? null,
                'item_category'    => $item['item_category']    ?? null,
                'item_code'        => $item['item_code']        ?? null,
                'item_description' => $item['item_description'] ?? null,
                'quantity'         => $item['quantity']         ?? 0,
                'unit'             => $item['unit']             ?? null,
                'unit_price'       => $item['unit_price']       ?? 0,
                'discount'         => $item['discount']         ?? 0,
                'amount'           => $item['amount']           ?? 0,
            ]);
        }

        if (!empty($data['payments']) && is_array($data['payments'])) {
            foreach ($data['payments'] as $payment) {
                $sale->payments()->create([
                    'payment_type' => $payment['payment_type'],
                    'bank_account_id' => $payment['bank_account_id'] ?? null,
                    'amount' => $payment['amount'],
                    'reference' => $payment['reference'] ?? null,
                ]);

                // If payment is for a bank account, update the opening balance
                if (!empty($payment['bank_account_id']) && !empty($payment['amount'])) {
                    $bank = BankAccount::find($payment['bank_account_id']);
                    if ($bank) {
                        $bank->opening_balance = ($bank->opening_balance ?? 0) + floatval($payment['amount']);
                        $bank->save();
                    }
                }
            }
        }

        if (!empty($data['source_estimate_id'])) {
            Sale::whereKey($data['source_estimate_id'])
                ->where('type', 'estimate')
                ->update(['status' => 'converted']);

            $sale->reference_id = $data['source_estimate_id'];
            $sale->save();
        }

        if (!empty($data['source_sale_order_id'])) {
            Sale::whereKey($data['source_sale_order_id'])
                ->where('type', 'sale_order')
                ->update(['status' => 'completed']);

            $sale->reference_id = $data['source_sale_order_id'];
            $sale->save();
        }

        if (!empty($data['source_challan_id'])) {
            Sale::whereKey($data['source_challan_id'])
                ->where('type', 'delivery_challan')
                ->update(['status' => 'closed']);

            $sale->reference_id = $data['source_challan_id'];
            $sale->save();
        }

        if (!empty($data['source_proforma_id'])) {
            Sale::whereKey($data['source_proforma_id'])
                ->where('type', 'proforma')
                ->update([
                    'status' => 'converted',
                ]);

            $sale->reference_id = $data['source_proforma_id'];
            $sale->save();
        }

        $redirectUrl = match ($sale->type) {
            'estimate' => route('sale.estimate'),
            'sale_order' => route('sale-order'),
            'proforma' => route('proforma-invoice'),
            default => route('sale.index'),
        };

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'bill_number' => $sale->bill_number,
            'redirect_url' => $redirectUrl,
        ]);
    }

    public function destroy(Sale $sale)
    {
        // Remove related items and payments first to avoid foreign key issues
        $sale->items()->delete();
        $sale->payments()->delete();

        $sale->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sale deleted successfully.',
        ]);
    }

    public function previewEstimate(Sale $sale)
    {
        if ($sale->type !== 'estimate') {
            abort(404);
        }

        $sale->load(['items']);

        return view('dashboard.sales.estimate-preview', compact('sale'));
    }

    public function printEstimate(Sale $sale)
    {
        if ($sale->type !== 'estimate') {
            abort(404);
        }

        $sale->load(['items']);

        return view('dashboard.sales.estimate-preview', ['sale' => $sale, 'autoPrint' => true]);
    }

    public function pdfEstimate(Sale $sale)
    {
        if ($sale->type !== 'estimate') {
            abort(404);
        }

        $sale->load(['items']);

        return view('dashboard.sales.estimate-preview', ['sale' => $sale, 'pdfMode' => true]);
    }

    public function previewSaleOrder(Sale $sale)
    {
        if ($sale->type !== 'sale_order') {
            abort(404);
        }

        $sale->load(['items']);

        return view('dashboard.saleorder.sale-order-preview', compact('sale'));
    }

    public function printSaleOrder(Sale $sale)
    {
        if ($sale->type !== 'sale_order') {
            abort(404);
        }

        $sale->load(['items']);

        return view('dashboard.saleorder.sale-order-preview', ['sale' => $sale, 'autoPrint' => true]);
    }

    public function pdfSaleOrder(Sale $sale)
    {
        if ($sale->type !== 'sale_order') {
            abort(404);
        }

        $sale->load(['items']);

        return view('dashboard.saleorder.sale-order-preview', ['sale' => $sale, 'pdfMode' => true]);
    }

    private function mapEstimateToSaleDraft(Sale $estimate, string $nextInvoiceNumber): array
    {
        return [
            'source_type' => 'estimate',
            'source_estimate_id' => $estimate->id,
            'party_id' => $estimate->party_id,
            'party_name' => $estimate->display_party_name,
            'phone' => $estimate->phone,
            'billing_address' => $estimate->billing_address,
            'bill_number' => $nextInvoiceNumber,
            'invoice_date' => optional($estimate->invoice_date)->format('Y-m-d') ?? now()->format('Y-m-d'),
            'total_qty' => $estimate->total_qty,
            'total_amount' => $estimate->total_amount,
            'discount_pct' => $estimate->discount_pct,
            'discount_rs' => $estimate->discount_rs,
            'tax_pct' => $estimate->tax_pct,
            'tax_amount' => $estimate->tax_amount,
            'round_off' => $estimate->round_off,
            'grand_total' => $estimate->grand_total,
            'received_amount' => 0,
            'balance' => $estimate->grand_total,
            'status' => 'Unpaid',
            'description' => $estimate->description,
            'image_path' => $estimate->image_path,
            'items' => $estimate->items->map(function ($item) {
                return [
                    'item_name' => $item->item_name,
                    'item_category' => $item->item_category,
                    'item_code' => $item->item_code,
                    'item_description' => $item->item_description,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount,
                    'amount' => $item->amount,
                ];
            })->values()->all(),
            'payments' => [],
        ];
    }

    private function mapSaleOrderToSaleDraft(Sale $saleOrder, string $nextInvoiceNumber): array
    {
        return [
            'source_type' => 'sale_order',
            'source_sale_order_id' => $saleOrder->id,
            'party_id' => $saleOrder->party_id,
            'party_name' => $saleOrder->display_party_name,
            'phone' => $saleOrder->phone,
            'billing_address' => $saleOrder->billing_address,
            'shipping_address' => $saleOrder->shipping_address,
            'bill_number' => $nextInvoiceNumber,
            'invoice_date' => now()->format('Y-m-d'),
            'total_qty' => $saleOrder->total_qty,
            'total_amount' => $saleOrder->total_amount,
            'discount_pct' => $saleOrder->discount_pct,
            'discount_rs' => $saleOrder->discount_rs,
            'tax_pct' => $saleOrder->tax_pct,
            'tax_amount' => $saleOrder->tax_amount,
            'round_off' => $saleOrder->round_off,
            'grand_total' => $saleOrder->grand_total,
            'received_amount' => 0,
            'balance' => $saleOrder->grand_total,
            'status' => 'Unpaid',
            'description' => $saleOrder->description,
            'image_path' => $saleOrder->image_path,
            'items' => $saleOrder->items->map(function ($item) {
                return [
                    'item_name' => $item->item_name,
                    'item_category' => $item->item_category,
                    'item_code' => $item->item_code,
                    'item_description' => $item->item_description,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount,
                    'amount' => $item->amount,
                ];
            })->values()->all(),
            'payments' => [],
        ];
    }

    private function mapDeliveryChallanToSaleDraft(Sale $challan, string $nextInvoiceNumber): array
    {
        return [
            'source_type' => 'delivery_challan',
            'source_challan_id' => $challan->id,
            'party_id' => $challan->party_id,
            'party_name' => $challan->display_party_name,
            'phone' => $challan->phone,
            'billing_address' => $challan->billing_address,
            'shipping_address' => $challan->shipping_address,
            'bill_number' => $nextInvoiceNumber,
            'invoice_date' => now()->format('Y-m-d'),
            'total_qty' => $challan->total_qty,
            'total_amount' => $challan->total_amount,
            'discount_pct' => $challan->discount_pct,
            'discount_rs' => $challan->discount_rs,
            'tax_pct' => $challan->tax_pct,
            'tax_amount' => $challan->tax_amount,
            'round_off' => $challan->round_off,
            'grand_total' => $challan->grand_total,
            'received_amount' => 0,
            'balance' => $challan->grand_total,
            'status' => 'Unpaid',
            'description' => $challan->description,
            'image_path' => $challan->image_path,
            'items' => $challan->items->map(function ($item) {
                return [
                    'item_name' => $item->item_name,
                    'item_category' => $item->item_category,
                    'item_code' => $item->item_code,
                    'item_description' => $item->item_description,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount,
                    'amount' => $item->amount,
                ];
            })->values()->all(),
            'payments' => [],
        ];
    }

    private function mapProformaToSaleDraft(Sale $proforma, string $nextInvoiceNumber): array
    {
        return [
            'source_type' => 'proforma',
            'source_proforma_id' => $proforma->id,
            'party_id' => $proforma->party_id,
            'party_name' => $proforma->display_party_name,
            'phone' => $proforma->phone,
            'billing_address' => $proforma->billing_address,
            'bill_number' => $nextInvoiceNumber,
            'invoice_date' => now()->format('Y-m-d'),
            'total_qty' => $proforma->total_qty,
            'total_amount' => $proforma->total_amount,
            'discount_pct' => $proforma->discount_pct,
            'discount_rs' => $proforma->discount_rs,
            'tax_pct' => $proforma->tax_pct,
            'tax_amount' => $proforma->tax_amount,
            'round_off' => $proforma->round_off,
            'grand_total' => $proforma->grand_total,
            'received_amount' => 0,
            'balance' => $proforma->grand_total,
            'status' => 'Unpaid',
            'description' => $proforma->description,
            'image_path' => $proforma->image_path,
            'items' => $proforma->items->map(function ($item) {
                return [
                    'item_name' => $item->item_name,
                    'item_category' => $item->item_category,
                    'item_code' => $item->item_code,
                    'item_description' => $item->item_description,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount,
                    'amount' => $item->amount,
                ];
            })->values()->all(),
            'payments' => [],
        ];
    }

    private function resolveStatusForType(
        string $type,
        float $receivedAmount,
        float $grandTotal,
        ?string $requestedStatus = null,
        ?string $currentStatus = null
    ): string {
        $allowedStatuses = match ($type) {
            'estimate' => ['open', 'pending', 'converted'],
            'proforma' => ['open', 'pending', 'converted'],
            'sale_order' => ['pending', 'confirmed', 'completed'],
            'delivery_challan' => ['open', 'closed'],
            default => ['Unpaid', 'Partial', 'Paid'],
        };

        if ($requestedStatus && in_array($requestedStatus, $allowedStatuses, true)) {
            return $requestedStatus;
        }

        if (in_array($type, ['estimate', 'proforma', 'sale_order', 'delivery_challan'], true)
            && $currentStatus
            && in_array($currentStatus, $allowedStatuses, true)) {
            return $currentStatus;
        }

        if ($type === 'estimate') return 'open';
        if ($type === 'sale_order') return 'pending';
        if ($type === 'delivery_challan') return 'open';

if ($receivedAmount >= $grandTotal && $grandTotal > 0) return 'Paid';
        if ($receivedAmount > 0 && $receivedAmount < $grandTotal) return 'Partial';

        return 'Unpaid';
    }
}