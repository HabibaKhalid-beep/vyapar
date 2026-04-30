<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Item;
use App\Models\Party;
use App\Models\Sale;
use App\Support\TransactionNumberPrefix;
use Illuminate\Http\Request;

class SaleOrderController extends Controller
{
    public function saleOrder(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $query = Sale::with(['items', 'payments', 'party'])
            ->where('type', 'sale_order')
            ->orderByDesc('created_at');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->whereHas('party', function ($partyQuery) use ($search) {
                        $partyQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('bill_number', 'like', "%{$search}%");
            });
        }

        $saleOrders = $query->get();
        $convertedInvoiceNumbers = Sale::where('type', 'invoice')
            ->whereNotNull('reference_id')
            ->whereIn('reference_id', $saleOrders->pluck('id'))
            ->pluck('bill_number', 'reference_id');

        $convertedInvoiceIds = Sale::where('type', 'invoice')
            ->whereNotNull('reference_id')
            ->whereIn('reference_id', $saleOrders->pluck('id'))
            ->pluck('id', 'reference_id');

        return view('dashboard.saleorder.sale-order', compact('saleOrders', 'search', 'convertedInvoiceNumbers', 'convertedInvoiceIds'));
    }

    public function create(Request $request)
    {
        $bankAccounts = BankAccount::active()->orderBy('display_name')->get();
        $items = Item::active()->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();
        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = TransactionNumberPrefix::format('sale_order', $nextSaleId);

        $saleOrder = null;
        $convertedSaleOrderData = null;

        if ($request->filled('edit_sale_id')) {
            $saleOrder = Sale::with(['items', 'payments', 'party'])->where('type', 'sale_order')->findOrFail($request->integer('edit_sale_id'));
        }

        if ($request->filled('duplicate_sale_id')) {
            $sourceSaleOrder = Sale::with(['items', 'payments', 'party'])->where('type', 'sale_order')->findOrFail($request->integer('duplicate_sale_id'));
            $convertedSaleOrderData = $sourceSaleOrder->toArray();
            $convertedSaleOrderData['bill_number'] = $nextInvoiceNumber;
            $convertedSaleOrderData['order_date'] = now()->toDateString();
            $convertedSaleOrderData['due_date'] = $sourceSaleOrder->due_date?->format('Y-m-d') ?: now()->toDateString();
            $convertedSaleOrderData['received_amount'] = 0;
            $convertedSaleOrderData['balance'] = $sourceSaleOrder->grand_total ?? $sourceSaleOrder->total_amount ?? 0;
            $convertedSaleOrderData['payments'] = [];
        }

        return view('dashboard.saleorder.create-sale-order', compact('bankAccounts', 'items', 'parties', 'nextInvoiceNumber', 'convertedSaleOrderData', 'saleOrder'));
    }

    public function createFromEstimate(Sale $sale)
    {
        if ($sale->type !== 'estimate') {
            abort(404);
        }

        if ($sale->status === 'converted') {
            return redirect()
                ->route('sale.estimate')
                ->with('error', 'This estimate is already converted.');
        }

        $bankAccounts = BankAccount::active()->orderBy('display_name')->get();
        $items = Item::active()->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();
        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = TransactionNumberPrefix::format('sale_order', $nextSaleId);

        $sale->load(['items']);

        $convertedSaleOrderData = [
            'source_type' => 'estimate',
            'source_estimate_id' => $sale->id,
            'party_id' => $sale->party_id,
            'party_name' => $sale->display_party_name,
            'phone' => $sale->phone,
            'billing_address' => $sale->billing_address,
            'shipping_address' => $sale->shipping_address,
            'bill_number' => $nextInvoiceNumber,
            'order_date' => now()->format('Y-m-d'),
            'due_date' => $sale->due_date ? $sale->due_date->format('Y-m-d') : now()->format('Y-m-d'),
            'total_qty' => $sale->total_qty,
            'total_amount' => $sale->total_amount,
            'discount_pct' => $sale->discount_pct,
            'discount_rs' => $sale->discount_rs,
            'tax_pct' => $sale->tax_pct,
            'tax_amount' => $sale->tax_amount,
            'round_off' => $sale->round_off,
            'grand_total' => $sale->grand_total,
            'balance' => $sale->grand_total,
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

        return view('dashboard.saleorder.create-sale-order', compact(
            'bankAccounts',
            'items',
            'parties',
            'nextInvoiceNumber',
            'convertedSaleOrderData'
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
                ->with('error', 'This proforma is already converted.');
        }

        $bankAccounts = BankAccount::active()->orderBy('display_name')->get();
        $items = Item::active()->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();
        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = TransactionNumberPrefix::format('sale_order', $nextSaleId);

        $sale->load(['items']);

        $convertedSaleOrderData = [
            'source_type' => 'proforma',
            'source_proforma_id' => $sale->id,
            'party_id' => $sale->party_id,
            'party_name' => $sale->display_party_name,
            'phone' => $sale->phone,
            'billing_address' => $sale->billing_address,
            'shipping_address' => $sale->shipping_address,
            'bill_number' => $nextInvoiceNumber,
            'order_date' => now()->format('Y-m-d'),
            'due_date' => now()->format('Y-m-d'),
            'total_qty' => $sale->total_qty,
            'total_amount' => $sale->total_amount,
            'discount_pct' => $sale->discount_pct,
            'discount_rs' => $sale->discount_rs,
            'tax_pct' => $sale->tax_pct,
            'tax_amount' => $sale->tax_amount,
            'round_off' => $sale->round_off,
            'grand_total' => $sale->grand_total,
            'balance' => $sale->grand_total,
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

        return view('dashboard.saleorder.create-sale-order', compact(
            'bankAccounts',
            'items',
            'parties',
            'nextInvoiceNumber',
            'convertedSaleOrderData'
        ));
    }
}
