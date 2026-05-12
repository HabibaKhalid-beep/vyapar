<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\BankAccount;
use App\Models\Broker;
use App\Models\Item;
use App\Models\Party;
use App\Models\PartyGroup;
use App\Models\Sale;
use App\Models\Warehouse;
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
                })->orWhere('bill_number', 'like', "%{$search}%");
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
        [$bankAccounts, $items, $parties, $brokers, $partyGroups, $warehouses, $customerPoDetailsEnabled] = $this->getFormDependencies();
        $nextInvoiceNumber = TransactionNumberPrefix::format('sale_order', (Sale::max('id') ?? 0) + 1);

        $sale = null;
        $convertedSaleData = null;

        if ($request->filled('edit_sale_id')) {
            $sale = Sale::with(['items', 'payments', 'party', 'details'])
                ->where('type', 'sale_order')
                ->findOrFail($request->integer('edit_sale_id'));
        }

        if ($request->filled('duplicate_sale_id')) {
            $sourceSaleOrder = Sale::with(['items', 'payments', 'party', 'details'])
                ->where('type', 'sale_order')
                ->findOrFail($request->integer('duplicate_sale_id'));

            $convertedSaleData = $this->mapSaleOrderToDraft($sourceSaleOrder, $nextInvoiceNumber);
        }

        return view('dashboard.saleorder.create-sale-order', compact(
            'bankAccounts',
            'items',
            'parties',
            'nextInvoiceNumber',
            'convertedSaleData',
            'sale',
            'brokers',
            'partyGroups',
            'warehouses',
            'customerPoDetailsEnabled'
        ));
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

        [$bankAccounts, $items, $parties, $brokers, $partyGroups, $warehouses, $customerPoDetailsEnabled] = $this->getFormDependencies();
        $nextInvoiceNumber = TransactionNumberPrefix::format('sale_order', (Sale::max('id') ?? 0) + 1);

        $sale->load(['items', 'details']);

        $convertedSaleData = $this->mapSourceSaleToOrderDraft($sale, $nextInvoiceNumber, [
            'source_type' => 'estimate',
            'source_estimate_id' => $sale->id,
        ]);

        return view('dashboard.saleorder.create-sale-order', compact(
            'bankAccounts',
            'items',
            'parties',
            'nextInvoiceNumber',
            'convertedSaleData',
            'brokers',
            'partyGroups',
            'warehouses',
            'customerPoDetailsEnabled'
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

        [$bankAccounts, $items, $parties, $brokers, $partyGroups, $warehouses, $customerPoDetailsEnabled] = $this->getFormDependencies();
        $nextInvoiceNumber = TransactionNumberPrefix::format('sale_order', (Sale::max('id') ?? 0) + 1);

        $sale->load(['items', 'details']);

        $convertedSaleData = $this->mapSourceSaleToOrderDraft($sale, $nextInvoiceNumber, [
            'source_type' => 'proforma',
            'source_proforma_id' => $sale->id,
        ]);

        return view('dashboard.saleorder.create-sale-order', compact(
            'bankAccounts',
            'items',
            'parties',
            'nextInvoiceNumber',
            'convertedSaleData',
            'brokers',
            'partyGroups',
            'warehouses',
            'customerPoDetailsEnabled'
        ));
    }

    private function getFormDependencies(): array
    {
        return [
            BankAccount::active()->orderBy('display_name')->get(),
            Item::active()->with('category')->orderBy('name')->get(),
            Party::orderBy('name')->get(),
            Broker::orderBy('name')->get(),
            PartyGroup::orderBy('name')->get(),
            Warehouse::where('is_active', true)->orderBy('name')->get(),
            AppSetting::getValue('customer_po_details_enabled', '0') === '1',
        ];
    }

    private function mapSaleOrderToDraft(Sale $saleOrder, string $nextInvoiceNumber): array
    {
        return $this->mapSourceSaleToOrderDraft($saleOrder, $nextInvoiceNumber, [
            'source_type' => 'sale_order',
            'source_sale_order_id' => $saleOrder->id,
        ]);
    }

    private function mapSourceSaleToOrderDraft(Sale $sale, string $nextInvoiceNumber, array $meta = []): array
    {
        $details = $sale->details;

        return array_merge([
            'party_id' => $sale->party_id,
            'party_name' => $sale->display_party_name,
            'phone' => $sale->phone,
            'billing_address' => $sale->billing_address,
            'shipping_address' => $sale->shipping_address,
            'bill_number' => $nextInvoiceNumber,
            'order_date' => now()->format('Y-m-d'),
            'due_date' => optional($sale->due_date)->format('Y-m-d') ?? now()->format('Y-m-d'),
            'details' => $details?->toArray(),
            'custom_expenses' => $details?->custom_expenses,
            'total_qty' => $sale->total_qty,
            'total_amount' => $sale->total_amount,
            'discount_pct' => $sale->discount_pct,
            'discount_rs' => $sale->discount_rs,
            'tax_pct' => $sale->tax_pct,
            'tax_amount' => $sale->tax_amount,
            'round_off' => $sale->round_off,
            'grand_total' => $sale->grand_total,
            'received_amount' => 0,
            'balance' => $sale->grand_total ?? $sale->total_amount ?? 0,
            'payments' => [],
            'items' => $sale->items->map(function ($item) {
                return [
                    'item_name' => $item->item_name,
                    'item_category' => $item->item_category,
                    'item_code' => $item->item_code,
                    'item_description' => $item->item_description,
                    'tafseel' => $item->tafseel,
                    'quantity' => $item->quantity,
                    'gross_w' => $item->gross_w,
                    'net_w' => $item->net_w,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'discount' => $item->discount,
                    'amount' => $item->amount,
                ];
            })->values()->all(),
        ], $meta);
    }
}
