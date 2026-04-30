<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Broker;
use App\Support\TransactionNumberPrefix;
use App\Models\Item;
use App\Models\Party;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $salesQuery = Sale::with(['payments', 'bankAccount', 'party'])
            ->where('type', 'invoice')
            ->whereNotIn('status', ['returned']);

        $period = (string) $request->query('period', 'all');
        $firm = (string) $request->query('firm', '');
        $from = $request->query('from');
        $to = $request->query('to');

        if ($firm !== '') {
            $salesQuery->whereHas('party', function ($query) use ($firm) {
                $query->where('name', $firm);
            });
        }

        $today = now();
        if ($period === 'this_month') {
            $salesQuery->whereDate('invoice_date', '>=', $today->copy()->startOfMonth()->toDateString())
                ->whereDate('invoice_date', '<=', $today->copy()->endOfMonth()->toDateString());
        } elseif ($period === 'last_month') {
            $last = $today->copy()->subMonth();
            $salesQuery->whereDate('invoice_date', '>=', $last->copy()->startOfMonth()->toDateString())
                ->whereDate('invoice_date', '<=', $last->copy()->endOfMonth()->toDateString());
        } elseif ($period === 'this_quarter') {
            $start = $today->copy()->firstOfQuarter();
            $end = $today->copy()->lastOfQuarter();
            $salesQuery->whereDate('invoice_date', '>=', $start->toDateString())
                ->whereDate('invoice_date', '<=', $end->toDateString());
        } elseif ($period === 'this_year') {
            $salesQuery->whereDate('invoice_date', '>=', $today->copy()->startOfYear()->toDateString())
                ->whereDate('invoice_date', '<=', $today->copy()->endOfYear()->toDateString());
        } elseif ($period === 'custom' && $from && $to) {
            $salesQuery->whereDate('invoice_date', '>=', $from)
                ->whereDate('invoice_date', '<=', $to);
        }

        if ($request->boolean('overdue')) {
            $salesQuery
                ->where('balance', '>', 0)
                ->whereDate('due_date', '<', now()->toDateString());
        }

        $sales = $salesQuery
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('dashboard.sales.sale_index', [
            'sales' => $sales,
            'showOverdueOnly' => $request->boolean('overdue'),
            'period' => $period,
            'firm' => $firm,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function create(Request $request, string $type = 'invoice')
    {
        $bankAccounts = BankAccount::active()->orderBy('display_name')->get();
        $brokers = Broker::orderBy('name')->get();
        $items = Item::active()->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $prefixTypeMap = [
            'invoice' => 'invoice',
            'estimate' => 'estimate',
            'sale_order' => 'sale_order',
            'proforma' => 'proforma_invoice',
            'delivery_challan' => 'delivery_challan',
            'sale_return' => 'credit_note',
            'pos' => 'invoice',
        ];
        $nextInvoiceNumber = TransactionNumberPrefix::format($prefixTypeMap[$type] ?? 'invoice', $nextSaleId);

        $convertedSaleData = null;
        if ($request->filled('duplicate_sale_id')) {
            $sourceSale = Sale::with(['items', 'payments', 'party'])->findOrFail($request->integer('duplicate_sale_id'));
            $convertedSaleData = $sourceSale->toArray();
            $convertedSaleData['bill_number'] = $nextInvoiceNumber;
            $convertedSaleData['invoice_date'] = now()->toDateString();
            $convertedSaleData['order_date'] = $sourceSale->order_date?->format('Y-m-d') ?: now()->toDateString();
            $convertedSaleData['deal_days'] = $sourceSale->deal_days ?? 0;
            $convertedSaleData['due_date'] = $sourceSale->due_date?->format('Y-m-d') ?: now()->toDateString();
            $convertedSaleData['received_amount'] = 0;
            $convertedSaleData['balance'] = $sourceSale->grand_total ?? $sourceSale->total_amount ?? 0;
            $convertedSaleData['status'] = $type === 'estimate' ? 'draft' : 'unpaid';
            $convertedSaleData['payments'] = [];
        }

        return view('dashboard.sales.create', compact('bankAccounts', 'brokers', 'items', 'parties', 'nextInvoiceNumber', 'type', 'convertedSaleData'));
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

        $bankAccounts = BankAccount::active()->orderBy('display_name')->get();
        $brokers = Broker::orderBy('name')->get();
        $items = Item::active()->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        $sale->load(['items']);

        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = TransactionNumberPrefix::format('invoice', $nextSaleId);
        $convertedSaleData = $this->mapEstimateToSaleDraft($sale, $nextInvoiceNumber);
        $type = 'invoice';

        return view('dashboard.sales.create', compact(
            'bankAccounts',
            'brokers',
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

        $bankAccounts = BankAccount::active()->orderBy('display_name')->get();
        $brokers = Broker::orderBy('name')->get();
        $items = Item::active()->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        $sale->load(['items']);

        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = TransactionNumberPrefix::format('invoice', $nextSaleId);
        $convertedSaleData = $this->mapSaleOrderToSaleDraft($sale, $nextInvoiceNumber);
        $type = 'invoice';

        return view('dashboard.sales.create', compact(
            'bankAccounts',
            'brokers',
            'items',
            'parties',
            'nextInvoiceNumber',
            'convertedSaleData',
            'type'
        ));
    }

    public function bulkConvertSaleOrders(Request $request)
    {
        $data = $request->validate([
            'sale_order_ids' => 'required|array|min:1',
            'sale_order_ids.*' => 'integer|exists:sales,id',
        ]);

        $saleOrders = Sale::with(['items', 'payments'])
            ->where('type', 'sale_order')
            ->whereIn('id', $data['sale_order_ids'])
            ->get();

        if ($saleOrders->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No sale orders found for conversion.',
            ], 422);
        }

        $createdSaleIds = [];

        DB::transaction(function () use ($saleOrders, &$createdSaleIds) {
            $nextSaleId = (Sale::max('id') ?? 0) + 1;

            foreach ($saleOrders as $saleOrder) {
                if ($saleOrder->status === 'completed') {
                    continue;
                }

                $nextInvoiceNumber = TransactionNumberPrefix::format('invoice', $nextSaleId);
                $nextSaleId += 1;

                $draft = $this->mapSaleOrderToSaleDraft($saleOrder, $nextInvoiceNumber);

                $paymentsReceived = (float) $saleOrder->payments->sum('amount');
                $receivedAmount = (float) ($saleOrder->received_amount ?? 0);
                $grandTotal = (float) ($draft['grand_total'] ?? $saleOrder->grand_total ?? 0);
                $balanceStored = (float) ($saleOrder->balance ?? 0);
                $receivedFromBalance = $grandTotal > 0 ? max($grandTotal - $balanceStored, 0) : 0;
                $receivedAmount = max($receivedAmount, $paymentsReceived, $receivedFromBalance);
                $balance = max(0, $grandTotal - $receivedAmount);

                $sale = Sale::create([
                    'type' => 'invoice',
                    'party_id' => $draft['party_id'] ?? $saleOrder->party_id,
                    'broker_id' => $saleOrder->broker_id,
                    'phone' => $draft['phone'] ?? $saleOrder->phone,
                    'billing_address' => $draft['billing_address'] ?? $saleOrder->billing_address,
                    'shipping_address' => $draft['shipping_address'] ?? $saleOrder->shipping_address,
                    'bill_number' => $draft['bill_number'] ?? $nextInvoiceNumber,
                    'invoice_date' => $draft['invoice_date'] ?? now()->toDateString(),
                    'order_date' => $saleOrder->order_date,
                    'deal_days' => $saleOrder->deal_days,
                    'due_date' => $saleOrder->due_date,
                    'reference_id' => $saleOrder->id,
                    'tadad' => $draft['tadad'] ?? $saleOrder->tadad,
                    'total_wazan' => $draft['total_wazan'] ?? $saleOrder->total_wazan,
                    'safi_wazan' => $draft['safi_wazan'] ?? $saleOrder->safi_wazan,
                    'rate' => $draft['rate'] ?? $saleOrder->rate,
                    'deo' => $draft['deo'] ?? $saleOrder->deo,
                    'total_qty' => $draft['total_qty'] ?? $saleOrder->total_qty,
                    'total_amount' => $draft['total_amount'] ?? $saleOrder->total_amount,
                    'discount_pct' => $draft['discount_pct'] ?? $saleOrder->discount_pct,
                    'discount_rs' => $draft['discount_rs'] ?? $saleOrder->discount_rs,
                    'tax_pct' => $draft['tax_pct'] ?? $saleOrder->tax_pct,
                    'tax_amount' => $draft['tax_amount'] ?? $saleOrder->tax_amount,
                    'round_off' => $draft['round_off'] ?? $saleOrder->round_off,
                    'grand_total' => $grandTotal,
                    'received_amount' => $receivedAmount,
                    'balance' => $balance,
                    'status' => $balance <= 0 ? 'Paid' : 'Unpaid',
                    'description' => $draft['description'] ?? $saleOrder->description,
                    'image_path' => $draft['image_path'] ?? $saleOrder->image_path,
                ]);

                foreach ($draft['items'] as $item) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'item_name' => $item['item_name'] ?? '',
                        'item_category' => $item['item_category'] ?? '',
                        'item_code' => $item['item_code'] ?? '',
                        'item_description' => $item['item_description'] ?? '',
                        'quantity' => $item['quantity'] ?? 0,
                        'unit' => $item['unit'] ?? '',
                        'unit_price' => $item['unit_price'] ?? 0,
                        'discount' => $item['discount'] ?? 0,
                        'amount' => $item['amount'] ?? 0,
                    ]);
                }

                $saleOrder->update(['status' => 'completed']);
                $sale->load('payments');
                $this->syncSaleLedgerEntries($sale, []);
                $this->recalculatePartyLedgerBalances($sale->party_id);

                $createdSaleIds[] = $sale->id;
            }
        });

        if (empty($createdSaleIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Selected sale orders are already converted.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'created_sale_ids' => $createdSaleIds,
        ]);
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

        $bankAccounts = BankAccount::active()->orderBy('display_name')->get();
        $brokers = Broker::orderBy('name')->get();
        $items = Item::active()->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        $sale->load(['items']);

        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = TransactionNumberPrefix::format('invoice', $nextSaleId);
        $convertedSaleData = $this->mapDeliveryChallanToSaleDraft($sale, $nextInvoiceNumber);
        $type = 'invoice';

        return view('dashboard.sales.create', compact(
            'bankAccounts',
            'brokers',
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

        $bankAccounts = BankAccount::active()->orderBy('display_name')->get();
        $brokers = Broker::orderBy('name')->get();
        $items = Item::active()->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        $sale->load(['items']);

        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = TransactionNumberPrefix::format('invoice', $nextSaleId);
        $convertedSaleData = $this->mapProformaToSaleDraft($sale, $nextInvoiceNumber);
        $type = 'invoice';

        return view('dashboard.sales.create', compact(
            'bankAccounts',
            'brokers',
            'items',
            'parties',
            'nextInvoiceNumber',
            'convertedSaleData',
            'type'
        ));
    }

    public function duplicate(Sale $sale)
    {
        if (!in_array($sale->type, ['invoice', 'pos'], true)) {
            abort(404);
        }

        $bankAccounts = BankAccount::active()->orderBy('display_name')->get();
        $brokers = Broker::orderBy('name')->get();
        $items = Item::active()->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        $sale->load(['items']);

        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = TransactionNumberPrefix::format('invoice', $nextSaleId);
        $convertedSaleData = $this->mapInvoiceToSaleDraft($sale, $nextInvoiceNumber);
        $type = $sale->type === 'pos' ? 'pos' : 'invoice';

        return view('dashboard.sales.create', compact(
            'bankAccounts',
            'brokers',
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
    $items = Item::active()
        ->where('type', 'product')
        ->orderBy('name')
        ->get([
            'id', 'name', 'item_code', 'unit',
            'sale_price', 'purchase_price', 'opening_qty',
        ]);

    $parties = Party::orderBy('name')
        ->get(['id', 'name', 'phone']);

    $bankAccounts = BankAccount::active()->orderBy('display_name')->get();

    $paymentModes = collect(['Cash', 'Card', 'UPI', 'Credit'])
        ->merge($bankAccounts->pluck('display_with_account'))
        ->unique()
        ->values()
        ->all();

    return compact('items', 'parties', 'paymentModes', 'bankAccounts');
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
        if ($sale->type === 'invoice' && strtolower((string) $sale->status) === 'cancelled') {
            return redirect()
                ->route('sale.index')
                ->with('error', 'Cancelled invoice cannot be edited.');
        }

        $bankAccounts = BankAccount::active()->orderBy('display_name')->get();
        $brokers = Broker::orderBy('name')->get();
        $items = Item::active()->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();
        $type = $sale->type ?? 'invoice';

        $sale->load(['items', 'payments', 'party']);

        $ledgerTransaction = Transaction::query()
            ->where('party_id', $sale->party_id)
            ->where('number', $sale->bill_number ?: (string) $sale->id)
            ->latest('id')
            ->first();

        if ($ledgerTransaction) {
            $sale->setAttribute('labour', $ledgerTransaction->labour);
            $sale->setAttribute('bardana', $ledgerTransaction->bardana);
            $sale->setAttribute('rehra_mazdori', $ledgerTransaction->rehra_mazdori);
            $sale->setAttribute('post_expense', $ledgerTransaction->post_expense);
            $sale->setAttribute('extra_expense', $ledgerTransaction->extra_expense);
        }

        // Provide full URLs for existing image/document if stored in the public disk
        if ($sale->image_path) {
            $sale->image_url = Storage::disk('public')->url($sale->image_path);
        }
        if ($sale->document_path) {
            $sale->document_url = Storage::disk('public')->url($sale->document_path);
        }

        return view('dashboard.sales.create', compact('bankAccounts', 'brokers', 'items', 'parties', 'sale', 'type'));
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
            'broker_id' => 'nullable|exists:brokers,id',
            'brokerage_type' => 'nullable|in:full,half,per_kg',
            'brokerage_rate' => 'nullable|numeric|min:0',
            'broker_amount' => 'nullable|numeric|min:0',
            'phone' => 'nullable|string|max:50',
            'billing_address' => 'nullable|string|max:1000',
            'shipping_address' => 'nullable|string|max:1000',
            'bill_number' => 'nullable|string|max:100',
            'invoice_date' => 'nullable|date',
            'order_date' => 'nullable|date',
            'deal_days' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date',
            'tadad' => 'nullable|integer|min:0',
            'total_wazan' => 'nullable|numeric|min:0',
            'safi_wazan' => 'nullable|numeric|min:0',
            'rate' => 'nullable|numeric|min:0',
            'deo' => 'nullable|numeric|min:0',
            'total_qty' => 'nullable|integer|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'labour' => 'nullable|numeric|min:0',
            'bardana' => 'nullable|numeric|min:0',
            'rehra_mazdori' => 'nullable|numeric|min:0',
            'post_expense' => 'nullable|numeric|min:0',
            'extra_expense' => 'nullable|numeric|min:0',
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
            'payments.*.direction' => 'nullable|in:payment_in,payment_out',
            'payments.*.bank_account_id' => 'nullable|exists:bank_accounts,id',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.reference' => 'nullable|string|max:255',
        ]);

        // When updating, treat payment entries as additional payments (keep previous received amount)
        $existingReceived = floatval($sale->received_amount ?? 0);
        $receivedAmount = $existingReceived;
        if (!empty($data['payments']) && is_array($data['payments'])) {
            foreach ($data['payments'] as $payment) {
                $receivedAmount += floatval($payment['amount'] ?? 0);
            }
        }

        $type = $data['type'] ?? $sale->type ?? 'invoice';
        $grandTotal = floatval($data['grand_total'] ?? 0);
        $balance = max(0, $grandTotal - $receivedAmount);
        $invoiceDate = !empty($data['invoice_date'])
            ? Carbon::parse($data['invoice_date'])
            : ($sale->invoice_date ? Carbon::parse($sale->invoice_date) : now());
        $orderDate = !empty($data['order_date'])
            ? Carbon::parse($data['order_date'])
            : $invoiceDate->copy();
        $dealDays = max(0, intval($data['deal_days'] ?? 0));
        $dueDate = $orderDate->copy()->addDays($dealDays);
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
            'broker_id' => $data['broker_id'] ?? $sale->broker_id,
            'brokerage_type' => $data['brokerage_type'] ?? null,
            'brokerage_rate' => $data['brokerage_rate'] ?? 0,
            'broker_amount' => $data['broker_amount'] ?? 0,
            'phone' => $data['phone'] ?? null,
            'billing_address' => $data['billing_address'] ?? null,
            'shipping_address' => $data['shipping_address'] ?? null,
            'bill_number' => $data['bill_number'] ?? $sale->bill_number,
            'invoice_date' => $invoiceDate->toDateString(),
            'order_date' => $orderDate->toDateString(),
            'deal_days' => $dealDays,
            'due_date' => $dueDate->toDateString(),
            'tadad' => $data['tadad'] ?? 0,
            'total_wazan' => $data['total_wazan'] ?? 0,
            'safi_wazan' => $data['safi_wazan'] ?? 0,
            'rate' => $data['rate'] ?? 0,
            'deo' => $data['deo'] ?? 0,
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
                $paymentAmount = floatval($payment['amount'] ?? 0);
                $rawPaymentType = (string) ($payment['payment_type'] ?? '');
                $normalizedPaymentType = strtolower($rawPaymentType);
                $isCash = $normalizedPaymentType === 'cash';
                $bankId = $payment['bank_account_id'] ?? null;
                $storePaymentType = $isCash ? 'cash' : $rawPaymentType;

                if ($paymentAmount <= 0) {
                    continue;
                }

                if (!$isCash && empty($bankId)) {
                    continue;
                }

                $direction = $this->normalizePaymentDirection($payment['direction'] ?? null);

                $cashAccount = null;
                if ($isCash) {
                    $cashAccount = BankAccount::cashAccount();
                    $bankId = $cashAccount->id;
                }

                $sale->payments()->create([
                    'payment_type' => $storePaymentType,
                    'direction' => $direction,
                    'bank_account_id' => $bankId,
                    'amount' => $paymentAmount,
                    'reference' => $payment['reference'] ?? null,
                ]);

                $bank = $isCash ? $cashAccount : BankAccount::find($bankId);
                if ($bank) {
                    $bank->opening_balance = ($bank->opening_balance ?? 0)
                        + ($direction === 'payment_out' ? -1 * $paymentAmount : $paymentAmount);
                    $bank->save();

                    $transactionType = $isCash
                        ? ($direction === 'payment_out' ? 'cash_out' : 'cash_in')
                        : ($direction === 'payment_out' ? 'sale_payment_out' : 'sale_payment');

                    $transactionDescription = $isCash
                        ? ($direction === 'payment_out'
                            ? 'Cash paid for invoice #' . ($sale->bill_number ?: $sale->id)
                            : 'Cash received for invoice #' . ($sale->bill_number ?: $sale->id))
                        : ($direction === 'payment_out'
                            ? 'Payment paid to party for invoice #' . ($sale->bill_number ?: $sale->id)
                            : 'Sale payment received for invoice #' . ($sale->bill_number ?: $sale->id));

                    BankTransaction::create([
                        'from_bank_account_id' => $bank->id,
                        'to_bank_account_id' => null,
                        'type' => $transactionType,
                        'amount' => $paymentAmount,
                        'transaction_date' => $sale->invoice_date ?? now()->toDateString(),
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'description' => $transactionDescription,
                        'meta' => [
                            'party_id' => $sale->party_id,
                            'payment_type' => $storePaymentType,
                            'reference' => $payment['reference'] ?? null,
                            'direction' => $direction,
                        ],
                    ]);
                }

            }
        }

        $sale->load('payments');
        $this->syncSaleLedgerEntries($sale, $data);
        $this->recalculatePartyLedgerBalances($sale->party_id);

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
            'share_url' => route('invoice', ['sale_id' => $sale->id]),
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
            'broker_id' => 'nullable|exists:brokers,id',
            'brokerage_type' => 'nullable|in:full,half,per_kg',
            'brokerage_rate' => 'nullable|numeric|min:0',
            'broker_amount' => 'nullable|numeric|min:0',
            'phone' => 'nullable|string|max:50',
            'billing_address' => 'nullable|string|max:1000',
            'shipping_address' => 'nullable|string|max:1000',
            'bill_number' => 'nullable|string|max:100',
            'invoice_date' => 'nullable|date',
            'order_date' => 'nullable|date',
            'deal_days' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date',
            'tadad' => 'nullable|integer|min:0',
            'total_wazan' => 'nullable|numeric|min:0',
            'safi_wazan' => 'nullable|numeric|min:0',
            'rate' => 'nullable|numeric|min:0',
            'deo' => 'nullable|numeric|min:0',
            'total_qty' => 'nullable|integer|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'labour' => 'nullable|numeric|min:0',
            'bardana' => 'nullable|numeric|min:0',
            'rehra_mazdori' => 'nullable|numeric|min:0',
            'post_expense' => 'nullable|numeric|min:0',
            'extra_expense' => 'nullable|numeric|min:0',
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
            'payments.*.direction' => 'nullable|in:payment_in,payment_out',
            'payments.*.bank_account_id' => 'nullable|exists:bank_accounts,id',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.reference' => 'nullable|string|max:255',
        ]);

        $receivedAmount = 0;

        if (!empty($data['payments']) && is_array($data['payments'])) {
            foreach ($data['payments'] as $payment) {
                $receivedAmount += floatval($payment['amount'] ?? 0);
            }
        }

        $type = $data['type'] ?? 'invoice';
        $grandTotal = floatval($data['grand_total'] ?? 0);
        $balance = max(0, $grandTotal - $receivedAmount);
        $invoiceDate = !empty($data['invoice_date'])
            ? Carbon::parse($data['invoice_date'])
            : ($type === 'sale_order' ? now() : now());
        $orderDate = !empty($data['order_date'])
            ? Carbon::parse($data['order_date'])
            : $invoiceDate->copy();
        $dealDays = max(0, intval($data['deal_days'] ?? 0));
        $dueDate = $orderDate->copy()->addDays($dealDays);
        $status = $this->resolveStatusForType(
            $type,
            $receivedAmount,
            $grandTotal,
            $data['status'] ?? null
        );

        $sale = Sale::create([
            'type' => $type,
            'party_id' => $data['party_id'] ?? null,
            'broker_id' => $data['broker_id'] ?? null,
            'brokerage_type' => $data['brokerage_type'] ?? null,
            'brokerage_rate' => $data['brokerage_rate'] ?? 0,
            'broker_amount' => $data['broker_amount'] ?? 0,
            'phone' => $data['phone'] ?? null,
            'billing_address' => $data['billing_address'] ?? null,
            'shipping_address' => $data['shipping_address'] ?? null,
            'bill_number' => $data['bill_number'] ?? null,
            'invoice_date' => $invoiceDate->toDateString(),
            'order_date' => $orderDate->toDateString(),
            'deal_days' => $dealDays,
            'due_date' => $dueDate->toDateString(),
            'tadad' => $data['tadad'] ?? 0,
            'total_wazan' => $data['total_wazan'] ?? 0,
            'safi_wazan' => $data['safi_wazan'] ?? 0,
            'rate' => $data['rate'] ?? 0,
            'deo' => $data['deo'] ?? 0,
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
                $paymentAmount = floatval($payment['amount'] ?? 0);
                $rawPaymentType = (string) ($payment['payment_type'] ?? '');
                $normalizedPaymentType = strtolower($rawPaymentType);
                $isCash = $normalizedPaymentType === 'cash';
                $bankId = $payment['bank_account_id'] ?? null;
                $storePaymentType = $isCash ? 'cash' : $rawPaymentType;

                if ($paymentAmount <= 0) {
                    continue;
                }

                if (!$isCash && empty($bankId)) {
                    continue;
                }

                $direction = $this->normalizePaymentDirection($payment['direction'] ?? null);

                $cashAccount = null;
                if ($isCash) {
                    $cashAccount = BankAccount::cashAccount();
                    $bankId = $cashAccount->id;
                }

                $sale->payments()->create([
                    'payment_type' => $storePaymentType,
                    'direction' => $direction,
                    'bank_account_id' => $bankId,
                    'amount' => $paymentAmount,
                    'reference' => $payment['reference'] ?? null,
                ]);

                $bank = $isCash ? $cashAccount : BankAccount::find($bankId);
                if ($bank) {
                    $bank->opening_balance = ($bank->opening_balance ?? 0)
                        + ($direction === 'payment_out' ? -1 * $paymentAmount : $paymentAmount);
                    $bank->save();

                    $transactionType = $isCash
                        ? ($direction === 'payment_out' ? 'cash_out' : 'cash_in')
                        : ($direction === 'payment_out' ? 'sale_payment_out' : 'sale_payment');

                    $transactionDescription = $isCash
                        ? ($direction === 'payment_out'
                            ? 'Cash paid for invoice #' . ($sale->bill_number ?: $sale->id)
                            : 'Cash received for invoice #' . ($sale->bill_number ?: $sale->id))
                        : ($direction === 'payment_out'
                            ? 'Payment paid to party for invoice #' . ($sale->bill_number ?: $sale->id)
                            : 'Additional payment received for invoice #' . ($sale->bill_number ?: $sale->id));

                    BankTransaction::create([
                        'from_bank_account_id' => $bank->id,
                        'to_bank_account_id' => null,
                        'type' => $transactionType,
                        'amount' => $paymentAmount,
                        'transaction_date' => $sale->invoice_date ?? now()->toDateString(),
                        'reference_type' => 'sale',
                        'reference_id' => $sale->id,
                        'description' => $transactionDescription,
                        'meta' => [
                            'party_id' => $sale->party_id,
                            'payment_type' => $storePaymentType,
                            'reference' => $payment['reference'] ?? null,
                            'direction' => $direction,
                        ],
                    ]);
                }

            }
        }

        $sale->load('payments');
        $this->syncSaleLedgerEntries($sale, $data);
        $this->recalculatePartyLedgerBalances($sale->party_id);

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
            'estimate' => route('invoice', ['sale_id' => $sale->id]),
            'sale_order' => route('invoice', ['sale_id' => $sale->id]),
            'proforma' => route('proforma-invoice'),
            default => route('sale.index'),
        };

        return response()->json([
            'success' => true,
            'sale_id' => $sale->id,
            'bill_number' => $sale->bill_number,
            'redirect_url' => $redirectUrl,
            'share_url' => route('invoice', ['sale_id' => $sale->id]),
        ]);
    }

    public function invoicePreview(Sale $sale)
    {
        $sale->loadMissing(['items.item', 'party', 'payments.bankAccount']);

        return view('themes.sales_invoice', [
            'sale' => $sale,
            'invoicePreviewData' => $this->mapSaleToThemePreviewData($sale),
            'pageTitle' => 'Preview',
            'browserTabLabel' => $sale->display_party_name !== '-' ? $sale->display_party_name : ('Invoice #' . ($sale->bill_number ?: $sale->id)),
            'saveCloseUrl' => route('sale.index'),
            'initialMode' => $sale->type === 'invoice' ? request()->query('mode', 'regular') : 'regular',
            'initialRegularThemeId' => (int) request()->query('theme_id', 1),
            'initialThermalThemeId' => (int) request()->query('theme_id', 1),
            'initialAccent' => (string) request()->query('accent', '#1f4e79'),
            'initialAccent2' => (string) request()->query('accent2', '#ff981f'),
        ]);
    }

    public function invoicePdf(Request $request, Sale $sale)
    {
        $sale->loadMissing(['items.item', 'party', 'payments.bankAccount']);

        $themeConfig = $this->resolveInvoiceThemeConfig(
            (string) $request->query('mode', 'regular'),
            (int) $request->query('theme_id', 1)
        );

        if ($request->boolean('download')) {
            $pdf = Pdf::loadView('themes.sales_invoice_pdf_document', [
                'invoicePreviewData' => $this->mapSaleToThemePreviewData($sale),
                'themeConfig' => $themeConfig,
                'accent' => (string) $request->query('accent', '#1f4e79'),
                'accent2' => (string) $request->query('accent2', '#ff981f'),
            ]);

            if (($themeConfig['mode'] ?? 'regular') === 'thermal') {
                $pdf->setPaper([0, 0, 226.77, 841.89], 'portrait');
            } else {
                $pdf->setPaper('a4', 'portrait');
            }

            return $pdf->download('invoice-' . ($sale->bill_number ?: $sale->id) . '.pdf');
        }

        return view('themes.sales_invoice_pdf', [
            'sale' => $sale,
            'invoicePreviewData' => $this->mapSaleToThemePreviewData($sale),
            'pageTitle' => 'Invoice PDF',
            'browserTabLabel' => 'Invoice #' . ($sale->bill_number ?: $sale->id),
            'saveCloseUrl' => route('sale.invoice-preview', $sale),
            'pdfMode' => true,
            'autoDownload' => $request->boolean('download'),
            'themeConfig' => $themeConfig,
            'initialMode' => $request->query('mode', 'regular'),
            'initialRegularThemeId' => (int) $request->query('theme_id', 1),
            'initialThermalThemeId' => (int) $request->query('theme_id', 1),
            'initialAccent' => (string) $request->query('accent', '#1f4e79'),
            'initialAccent2' => (string) $request->query('accent2', '#ff981f'),
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

    public function deliveryPreview(Sale $sale)
    {
        abort_unless($sale->type === 'invoice', 404);

        $sale->loadMissing(['items', 'party']);

        return view('dashboard.delivery.challan-preview', [
            'sale' => $sale,
            'previewTitle' => 'Delivery Challan',
            'documentNumberLabel' => 'Invoice No.',
            'documentDateLabel' => 'Date',
            'showRates' => false,
            'showAmount' => false,
        ]);
    }

    public function paymentHistory(Sale $sale)
    {
        abort_unless($sale->type === 'invoice', 404);

        $sale->loadMissing(['payments.bankAccount']);

        return response()->json([
            'sale_id' => $sale->id,
            'bill_number' => $sale->bill_number ?: $sale->id,
            'grand_total' => (float) ($sale->grand_total ?? 0),
            'received_amount' => (float) ($sale->received_amount ?? 0),
            'balance' => (float) ($sale->balance ?? 0),
            'payments' => $sale->payments->map(function ($payment) use ($sale) {
                return [
                    'payment_type' => $payment->payment_type ?: '-',
                    'bank_name' => $payment->bankAccount?->display_name ?: '-',
                    'amount' => (float) ($payment->amount ?? 0),
                    'reference' => $payment->reference ?: '-',
                    'date' => $this->formatPreviewDate($sale->invoice_date ?: $sale->created_at),
                ];
            })->values(),
        ]);
    }

    public function bankHistory(Sale $sale)
    {
        abort_unless($sale->type === 'invoice', 404);

        $sale->loadMissing(['payments.bankAccount']);

        $transactions = BankTransaction::with(['fromBankAccount'])
            ->where('reference_type', 'sale')
            ->where('reference_id', $sale->id)
            ->orderByDesc('transaction_date')
            ->get()
            ->map(function ($transaction) {
                return [
                    'bank_name' => $transaction->fromBankAccount?->display_name ?: '-',
                    'amount' => (float) ($transaction->amount ?? 0),
                    'type' => (string) ($transaction->type ?: 'sale_payment'),
                    'reference' => (string) ($transaction->description ?: '-'),
                    'date' => $this->formatPreviewDate($transaction->transaction_date),
                ];
            });

        if ($transactions->isEmpty()) {
            $transactions = $sale->payments->map(function ($payment) use ($sale) {
                return [
                    'bank_name' => $payment->bankAccount?->display_name ?: '-',
                    'amount' => (float) ($payment->amount ?? 0),
                    'type' => 'sale_payment',
                    'reference' => $payment->reference ?: '-',
                    'date' => $this->formatPreviewDate($sale->invoice_date ?: $sale->created_at),
                ];
            });
        }

        return response()->json([
            'sale_id' => $sale->id,
            'bill_number' => $sale->bill_number ?: $sale->id,
            'entries' => $transactions->values(),
        ]);
    }

    public function cancel(Sale $sale)
    {
        abort_unless($sale->type === 'invoice', 404);

        if (strtolower((string) $sale->status) === 'cancelled') {
            return response()->json([
                'success' => true,
                'message' => 'Invoice already cancelled.',
                'status' => 'Cancelled',
            ]);
        }

        $sale->update(['status' => 'Cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Invoice cancelled successfully.',
            'status' => 'Cancelled',
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

    private function mapSaleToThemePreviewData(Sale $sale): array
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

        $items = $sale->items->map(function ($item) use ($sale) {
            $taxPct = $this->formatPercentValue($sale->tax_pct);

            return [
                'name' => $item->item_name ?: ($item->item?->name ?: 'Item'),
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
                'gst' => $this->formatPercentValue($sale->tax_pct),
                'amt' => 0,
            ];
        }

        $invoiceDate = $this->formatPreviewDate($sale->invoice_date ?: $sale->created_at);
        $dueDate = $this->formatPreviewDate($sale->due_date ?: $sale->invoice_date ?: $sale->created_at);
        $createdAt = $sale->created_at instanceof Carbon ? $sale->created_at : Carbon::parse($sale->created_at);
        $businessName = trim((string) config('app.name', 'My Company')) ?: 'My Company';
        $partyName = $sale->display_party_name !== '-' ? $sale->display_party_name : 'Walk-in Customer';

        $paymentsReceived = (float) $sale->payments
            ->sum('amount');

        $totalAmount = (float) ($sale->grand_total ?? 0);
        $storedBalance = (float) ($sale->balance ?? 0);

        $receivedAmount = (float) ($sale->received_amount ?? 0);
        $receivedFromBalance = $totalAmount > 0 ? max($totalAmount - $storedBalance, 0) : 0;
        $receivedAmount = max($receivedAmount, $paymentsReceived, $receivedFromBalance);

        return [
            'title' => $sale->type === 'invoice' ? 'Invoice' : ucwords(str_replace('_', ' ', (string) $sale->type)),
            'businessName' => $businessName,
            'phone' => (string) ($sale->phone ?: ($sale->party?->phone ?: '')),
            'invoiceNo' => (string) ($sale->bill_number ?: $sale->id),
            'date' => $invoiceDate,
            'time' => $createdAt->format('h:i A'),
            'dueDate' => $dueDate,
            'billTo' => $partyName,
            'billAddress' => (string) ($sale->billing_address ?: ''),
            'billPhone' => (string) ($sale->phone ?: ($sale->party?->phone ?: '')),
            'shipTo' => (string) ($sale->shipping_address ?: $sale->billing_address ?: ''),
            'items' => $items,
            'description' => (string) ($sale->description ?: 'Thanks for doing business with us!'),
            'subtotal' => (float) ($sale->total_amount ?? 0),
            'discount' => (float) ($sale->discount_rs ?? 0),
            'taxAmount' => (float) ($sale->tax_amount ?? 0),
            'total' => $totalAmount,
            'received' => $receivedAmount,
            'balance' => (float) ($sale->balance ?? max($totalAmount - $receivedAmount, 0)),
            'bankName' => (string) ($bankAccount?->bank_name ?: $bankAccount?->display_name ?: ''),
            'bankAccountNumber' => (string) ($bankAccount?->account_number ?: ''),
            'bankAccountHolder' => (string) ($bankAccount?->account_holder_name ?: ''),
        ];
    }

    private function formatPreviewDate($value): string
    {
        if (empty($value)) {
            return now()->format('d/m/Y');
        }

        try {
            return Carbon::parse($value)->format('d/m/Y');
        } catch (\Throwable $exception) {
            return now()->format('d/m/Y');
        }
    }

    private function formatPercentValue($value): string
    {
        $numeric = (float) ($value ?? 0);
        $formatted = number_format($numeric, 2, '.', '');
        $formatted = rtrim(rtrim($formatted, '0'), '.');

        return ($formatted === '' ? '0' : $formatted) . '%';
    }

    private function resolveInvoiceThemeConfig(string $mode, int $themeId): array
    {
        $mode = $mode === 'thermal' ? 'thermal' : 'regular';

        $themes = $mode === 'thermal'
            ? [
                1 => ['name' => 'Thermal Theme 1', 'variant' => 'thermal1'],
                2 => ['name' => 'Thermal Theme 2', 'variant' => 'thermal2'],
                3 => ['name' => 'Thermal Theme 3', 'variant' => 'thermal3'],
                4 => ['name' => 'Thermal Theme 4', 'variant' => 'thermal4'],
                5 => ['name' => 'Thermal Theme 5', 'variant' => 'thermal5'],
            ]
            : [
                1 => ['name' => 'Telly Theme', 'variant' => 'classicA'],
                2 => ['name' => 'Landscape Theme 1', 'variant' => 'purpleA'],
                3 => ['name' => 'Landscape Theme 2', 'variant' => 'classicB'],
                4 => ['name' => 'Tax Theme 1', 'variant' => 'purpleB'],
                5 => ['name' => 'Tax Theme 2', 'variant' => 'classicC'],
                6 => ['name' => 'Tax Theme 3', 'variant' => 'modernPurple'],
                7 => ['name' => 'Tax Theme 4', 'variant' => 'purpleC'],
                8 => ['name' => 'Tax Theme 5', 'variant' => 'classicSale'],
                9 => ['name' => 'Tax Theme 6', 'variant' => 'taxTheme6'],
                10 => ['name' => 'Double Divine', 'variant' => 'doubleDivine'],
                11 => ['name' => 'French Elite', 'variant' => 'frenchElite'],
                12 => ['name' => 'Theme 1', 'variant' => 'theme1'],
                13 => ['name' => 'Theme 2', 'variant' => 'theme2'],
                14 => ['name' => 'Theme 3', 'variant' => 'theme3'],
                15 => ['name' => 'Theme 4', 'variant' => 'theme4'],
            ];

        $theme = $themes[$themeId] ?? reset($themes);

        return [
            'id' => $themeId,
            'mode' => $mode,
            'name' => $theme['name'],
            'variant' => $theme['variant'],
        ];
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
            'order_date' => optional($estimate->order_date)->format('Y-m-d') ?? optional($estimate->invoice_date)->format('Y-m-d') ?? now()->format('Y-m-d'),
            'deal_days' => $estimate->deal_days ?? 0,
            'due_date' => optional($estimate->due_date)->format('Y-m-d') ?? optional($estimate->invoice_date)->format('Y-m-d') ?? now()->format('Y-m-d'),
            'tadad' => $estimate->tadad,
            'total_wazan' => $estimate->total_wazan,
            'safi_wazan' => $estimate->safi_wazan,
            'rate' => $estimate->rate,
            'deo' => $estimate->deo,
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

    private function mapInvoiceToSaleDraft(Sale $sale, string $nextInvoiceNumber): array
    {
        return [
            'source_type' => $sale->type,
            'party_id' => $sale->party_id,
            'party_name' => $sale->display_party_name,
            'phone' => $sale->phone,
            'billing_address' => $sale->billing_address,
            'shipping_address' => $sale->shipping_address,
            'bill_number' => $nextInvoiceNumber,
            'invoice_date' => now()->format('Y-m-d'),
            'order_date' => optional($sale->order_date)->format('Y-m-d') ?? now()->format('Y-m-d'),
            'deal_days' => $sale->deal_days ?? 0,
            'due_date' => optional($sale->due_date)->format('Y-m-d') ?? now()->format('Y-m-d'),
            'tadad' => $sale->tadad,
            'total_wazan' => $sale->total_wazan,
            'safi_wazan' => $sale->safi_wazan,
            'rate' => $sale->rate,
            'deo' => $sale->deo,
            'total_qty' => $sale->total_qty,
            'total_amount' => $sale->total_amount,
            'discount_pct' => $sale->discount_pct,
            'discount_rs' => $sale->discount_rs,
            'tax_pct' => $sale->tax_pct,
            'tax_amount' => $sale->tax_amount,
            'round_off' => $sale->round_off,
            'grand_total' => $sale->grand_total,
            'received_amount' => 0,
            'balance' => $sale->grand_total,
            'status' => 'Unpaid',
            'description' => $sale->description,
            'image_path' => $sale->image_path,
            'items' => $sale->items->map(function ($item) {
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
            'order_date' => optional($saleOrder->order_date)->format('Y-m-d') ?? now()->format('Y-m-d'),
            'deal_days' => $saleOrder->deal_days ?? 0,
            'due_date' => optional($saleOrder->due_date)->format('Y-m-d') ?? now()->format('Y-m-d'),
            'tadad' => $saleOrder->tadad,
            'total_wazan' => $saleOrder->total_wazan,
            'safi_wazan' => $saleOrder->safi_wazan,
            'rate' => $saleOrder->rate,
            'deo' => $saleOrder->deo,
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
            'order_date' => optional($challan->order_date)->format('Y-m-d') ?? now()->format('Y-m-d'),
            'deal_days' => $challan->deal_days ?? 0,
            'due_date' => optional($challan->due_date)->format('Y-m-d') ?? now()->format('Y-m-d'),
            'tadad' => $challan->tadad,
            'total_wazan' => $challan->total_wazan,
            'safi_wazan' => $challan->safi_wazan,
            'rate' => $challan->rate,
            'deo' => $challan->deo,
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
            'order_date' => optional($proforma->order_date)->format('Y-m-d') ?? now()->format('Y-m-d'),
            'deal_days' => $proforma->deal_days ?? 0,
            'due_date' => optional($proforma->due_date)->format('Y-m-d') ?? now()->format('Y-m-d'),
            'tadad' => $proforma->tadad,
            'total_wazan' => $proforma->total_wazan,
            'safi_wazan' => $proforma->safi_wazan,
            'rate' => $proforma->rate,
            'deo' => $proforma->deo,
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

    private function shouldCreateLedgerForSaleType(string $type): bool
    {
        return in_array($type, ['invoice', 'pos', 'sale_return'], true);
    }

    private function resolveLedgerTypeFromSale(string $type): string
    {
        return match ($type) {
            'sale_return' => 'sale_return',
            default => 'sale',
        };
    }

    private function calculateLedgerExpenseTotal(array $data): float
    {
        return floatval($data['broker_amount'] ?? 0)
            + floatval($data['labour'] ?? 0)
            + floatval($data['bardana'] ?? 0)
            + floatval($data['rehra_mazdori'] ?? 0)
            + floatval($data['parcel_expense'] ?? 0)
            + floatval($data['post_expense'] ?? 0)
            + floatval($data['extra_expense'] ?? 0);
    }

    private function deleteSaleLedgerTransactions(Sale $sale): void
    {
        if (empty($sale->party_id) || empty($sale->bill_number)) {
            return;
        }

        Transaction::query()
            ->where('party_id', $sale->party_id)
            ->where(function ($query) use ($sale) {
                $query->where(function ($subQuery) use ($sale) {
                    $subQuery->where('number', $sale->bill_number)
                        ->whereIn('type', ['sale', 'sale_return']);
                })->orWhere(function ($subQuery) use ($sale) {
                    $subQuery->where('number', 'like', 'PAY-' . ($sale->bill_number ?: $sale->id) . '-%');
                });
            })
            ->delete();
    }

    private function syncSaleLedgerEntries(Sale $sale, array $data): void
    {
        if (empty($sale->party_id) || !$this->shouldCreateLedgerForSaleType((string) $sale->type)) {
            return;
        }

        $this->deleteSaleLedgerTransactions($sale);

        $saleAmount = floatval($sale->grand_total ?? 0);
        $ledgerType = $this->resolveLedgerTypeFromSale((string) $sale->type);

        $transactionPayload = [
            'party_id' => $sale->party_id,
            'type' => $ledgerType,
            'number' => $sale->bill_number ?: (string) $sale->id,
            'date' => $sale->invoice_date ?? now(),
            'total' => $saleAmount,
            'debit' => $ledgerType === 'sale_return' ? 0 : $saleAmount,
            'credit' => $ledgerType === 'sale_return' ? $saleAmount : 0,
            'paid_amount' => floatval($sale->received_amount ?? 0),
            'balance' => floatval($sale->balance ?? 0),
            'running_balance' => 0,
            'due_date' => $sale->due_date,
            'status' => $sale->status,
            'broker_id' => $sale->broker_id,
            'broker_amount' => floatval($data['broker_amount'] ?? 0),
            'labour' => floatval($data['labour'] ?? 0),
            'bardana' => floatval($data['bardana'] ?? 0),
            'parcel_expense' => floatval($data['parcel_expense'] ?? 0),
            'post_expense' => floatval($data['post_expense'] ?? 0),
            'extra_expense' => floatval($data['extra_expense'] ?? 0),
            'description' => 'Invoice #' . ($sale->bill_number ?: $sale->id),
        ];

        if (Schema::hasColumn('transactions', 'rehra_mazdori')) {
            $transactionPayload['rehra_mazdori'] = floatval($data['rehra_mazdori'] ?? 0);
        }

        Transaction::create($transactionPayload);

        foreach ($sale->payments()->orderBy('id')->get() as $paymentRecord) {
            $paymentAmount = floatval($paymentRecord->amount ?? 0);
            $paymentDirection = $this->normalizePaymentDirection($paymentRecord->direction ?? null);

            Transaction::create([
                'party_id' => $sale->party_id,
                'type' => $paymentDirection,
                'number' => 'PAY-' . ($sale->bill_number ?: $sale->id) . '-' . $paymentRecord->id,
                'date' => $sale->invoice_date ?? now(),
                'total' => $paymentAmount,
                'debit' => 0,
                'credit' => $paymentAmount,
                'paid_amount' => $paymentAmount,
                'balance' => floatval($sale->balance ?? 0),
                'running_balance' => 0,
                'status' => 'paid',
                'description' => $paymentDirection === 'payment_out'
                    ? 'Payment paid to party for Invoice #' . ($sale->bill_number ?: $sale->id)
                    : 'Payment received for Invoice #' . ($sale->bill_number ?: $sale->id),
            ]);
        }
    }

    private function normalizePaymentDirection(?string $direction): string
    {
        return 'payment_in';
    }

    private function recalculatePartyLedgerBalances(?int $partyId): void
    {
        if (empty($partyId)) {
            return;
        }

        $runningBalance = 0.0;

        Transaction::where('party_id', $partyId)
            ->orderBy('date')
            ->orderBy('id')
            ->get()
            ->each(function (Transaction $transaction) use (&$runningBalance) {
                $runningBalance += Transaction::normalizeLedgerAmount($transaction->debit ?? 0);
                $runningBalance -= Transaction::normalizeLedgerAmount($transaction->credit ?? 0);
                $runningBalance = Transaction::normalizeLedgerAmount($runningBalance);
                $transaction->running_balance = $runningBalance;
                $transaction->saveQuietly();
            });

        Transaction::syncPartyCurrentBalance($partyId);
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

 public function paymentIn()
{
    $parties = Party::all();
    $bankAccounts = BankAccount::active()->get();

    return view('dashboard.sales.payement-in', compact('parties', 'bankAccounts'));
}

}
