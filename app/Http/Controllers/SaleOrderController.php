<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Item;
use App\Models\Party;
use App\Models\Sale;
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
                $builder->where('party_name', 'like', "%{$search}%")
                    ->orWhereHas('party', function ($partyQuery) use ($search) {
                        $partyQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('bill_number', 'like', "%{$search}%");
            });
        }

        $saleOrders = $query->get();
        $convertedInvoices = Sale::where('type', 'invoice')
            ->whereNotNull('reference_id')
            ->whereIn('reference_id', $saleOrders->pluck('id'))
            ->pluck('bill_number', 'reference_id');

        return view('dashboard.saleorder.sale-order', compact('saleOrders', 'search', 'convertedInvoices'));
    }

    public function create()
    {
        $bankAccounts = BankAccount::orderBy('display_name')->get();
        $items = Item::orderBy('name')->get();
        $parties = Party::orderBy('name')->get();
        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = 'SO-' . str_pad($nextSaleId, 4, '0', STR_PAD_LEFT);

        return view('dashboard.saleorder.create-sale-order', compact('bankAccounts', 'items', 'parties', 'nextInvoiceNumber'));
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

        $bankAccounts = BankAccount::orderBy('display_name')->get();
        $items = Item::orderBy('name')->get();
        $parties = Party::orderBy('name')->get();
        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = 'SO-' . str_pad($nextSaleId, 4, '0', STR_PAD_LEFT);

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
}
