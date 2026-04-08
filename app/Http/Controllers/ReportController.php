<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Party;
use App\Models\Category;

class ReportController extends Controller
{

   public function index()
{
    $categories = Category::all();
    $parties    = Party::all();

    $items = \DB::table('items')->orderBy('name')->get(['id', 'name', 'category_id']);

    // Stock Summary
    $stockSummary = \DB::table('items')
        ->select(
            'id', 'name', 'category_id',
            'sale_price', 'purchase_price',
            \DB::raw('opening_qty as stock_qty'),
            \DB::raw('opening_qty * purchase_price as stock_value')
        )
        ->get();

    $stockSummaryTotals = [
        'qty'   => $stockSummary->sum('stock_qty'),
        'value' => $stockSummary->sum('stock_value'),
    ];

    // Low Stock
    $lowStock = \DB::table('items')
        ->select('id', 'name', 'category_id',
            \DB::raw('opening_qty as stock_qty'),
            \DB::raw('min_stock as min_stock_qty'),
            \DB::raw('opening_qty * purchase_price as stock_value')
        )
        ->whereRaw('opening_qty <= min_stock')
        ->get();

    // Stock Detail
    $stockDetail = \DB::table('items')
        ->select(
            'id', 'name', 'category_id',
            \DB::raw('opening_qty as beginning_qty'),
            \DB::raw('0 as qty_in'),
            \DB::raw('0 as qty_out'),
            \DB::raw('0 as purchase_amount'),
            \DB::raw('0 as sale_amount'),
            \DB::raw('opening_qty as closing_qty')
        )
        ->get();

    $stockDetailTotals = [
        'beginning_qty'   => $stockDetail->sum('beginning_qty'),
        'qty_in'          => 0,
        'qty_out'         => 0,
        'purchase_amount' => 0,
        'sale_amount'     => 0,
        'closing_qty'     => $stockDetail->sum('closing_qty'),
    ];

    // Item Wise P&L (basic)
    $itemWisePnL = \DB::table('items')
        ->select(
            'name',
            \DB::raw('0 as sale'),
            \DB::raw('0 as cr_note'),
            \DB::raw('0 as purchase'),
            \DB::raw('0 as dr_note'),
            \DB::raw('opening_qty * purchase_price as opening_stock'),
            \DB::raw('opening_qty * purchase_price as closing_stock'),
            \DB::raw('0 as tax_receivable'),
            \DB::raw('0 as tax_payable'),
            \DB::raw('0 as mfg_cost'),
            \DB::raw('0 as consumption_cost'),
            \DB::raw('0 as net_profit')
        )
        ->get();

    $itemWisePnLTotal = 0;

    $stockSummaryByCat = \DB::table('items')
        ->join('categories', 'categories.id', '=', 'items.category_id')
        ->select(
            'categories.name as category_name',
            \DB::raw('SUM(opening_qty) as stock_qty'),
            \DB::raw('SUM(opening_qty * purchase_price) as stock_value')
        )
        ->groupBy('categories.id', 'categories.name')
        ->get();

    $partyReport = collect();
    $partyReportTotals = ['sale_qty' => 0, 'sale_amount' => 0, 'purchase_qty' => 0, 'purchase_amount' => 0];
    $itemCategoryPnL = collect();
    $salePurchaseByCat = collect();
    $itemWiseDiscount = collect();
    $itemDetail = collect();

    return view('dashboard.report', compact(
        'categories', 'parties', 'items',
        'stockSummary', 'stockSummaryTotals',
        'lowStock', 'stockDetail', 'stockDetailTotals',
        'itemWisePnL', 'itemWisePnLTotal',
        'stockSummaryByCat', 'partyReport', 'partyReportTotals',
        'itemCategoryPnL', 'salePurchaseByCat',
        'itemWiseDiscount', 'itemDetail'
    ));
}
    // ─── HELPER: parse date range from request ───────────────
    private function dateRange(Request $request): array
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to   = $request->input('to',   now()->toDateString());
        return [$from, $to];
    }

    // ─── HELPER: format amount ────────────────────────────────
    private function fmt($val): float
    {
        return round((float) ($val ?? 0), 2);
    }

    // ============================================================
    // 1. PARTY STATEMENT
    // ============================================================
    public function partyStatement(Request $request, $partyId)
    {
        $party = Party::findOrFail($partyId);
        [$from, $to] = $this->dateRange($request);

        $rows = collect();

        if (\Schema::hasTable('sales')) {
            $sales = DB::table('sales')
                ->where('party_id', $partyId)
                ->whereBetween('invoice_date', [$from, $to])
                ->select(
                    'invoice_date as date',
                    DB::raw("'Sale' as type"),
                    'bill_number as reference',
                    DB::raw("'Cash' as payment_type"),
                    'total_amount as debit',
                    DB::raw('0 as credit')
                )->get();
            $rows = $rows->merge($sales);
        }

        if (\Schema::hasTable('purchases')) {
            $purchases = DB::table('purchases')
                ->where('party_id', $partyId)
                ->whereBetween('bill_date', [$from, $to])
                ->select(
                    'bill_date as date',
                    DB::raw("'Purchase' as type"),
                    'bill_number as reference',
                    DB::raw("'Cash' as payment_type"),
                    DB::raw('0 as debit'),
                    'total_amount as credit'
                )->get();
            $rows = $rows->merge($purchases);
        }

        if (\Schema::hasTable('payment_ins')) {
            $payIn = DB::table('payment_ins')
                ->where('party_id', $partyId)
                ->whereBetween('date', [$from, $to])
                ->select(
                    'date as date',
                    DB::raw("'Payment-In' as type"),
                    'reference_no as reference',
                    'payment_type',
                    'amount as debit',
                    DB::raw('0 as credit')
                )->get();
            $rows = $rows->merge($payIn);
        }

        $rows = $rows->sortBy('date')->values();

        $openingBalance = $this->fmt($party->opening_balance ?? 0);
        $running = $openingBalance;
        $totalDebit = 0;
        $totalCredit = 0;
        $totalSale = 0;
        $totalPurchase = 0;

        $transactions = $rows->map(function ($r) use (&$running, &$totalDebit, &$totalCredit, &$totalSale, &$totalPurchase) {
            $debit  = $this->fmt($r->debit ?? 0);
            $credit = $this->fmt($r->credit ?? 0);
            $running += ($debit - $credit);
            $totalDebit   += $debit;
            $totalCredit  += $credit;
            if (str_contains(strtolower($r->type ?? ''), 'sale')) $totalSale += $debit;
            if (str_contains(strtolower($r->type ?? ''), 'purchase')) $totalPurchase += $credit;

            return [
                'date'            => $r->date,
                'type'            => $r->type,
                'reference'       => $r->reference ?? '-',
                'payment_type'    => $r->payment_type ?? 'Cash',
                'debit'           => $debit ?: null,
                'credit'          => $credit ?: null,
                'running_balance' => $this->fmt($running),
            ];
        })->values()->toArray();

        return response()->json([
            'success'          => true,
            'transactions'     => $transactions,
            'opening_balance'  => $openingBalance,
            'closing_balance'  => $this->fmt($running),
            'total_debit'      => $this->fmt($totalDebit),
            'total_credit'     => $this->fmt($totalCredit),
            'total_sale'       => $this->fmt($totalSale),
            'total_purchase'   => $this->fmt($totalPurchase),
            'total_money_in'   => $this->fmt($totalDebit),
            'total_money_out'  => $this->fmt($totalCredit),
            'total_receivable' => $this->fmt($party->current_balance ?? 0),
        ]);
    }

    // ============================================================
    // 2. ALL PARTIES
    // ============================================================
    public function allParties(Request $request)
    {
        $query = Party::query();

        if ($request->filled('type')) {
            if ($request->type === 'receivable') {
                $query->where('current_balance', '>', 0);
            } elseif ($request->type === 'payable') {
                $query->where('current_balance', '<', 0);
            }
        }

        $parties = $query->get()->map(function ($p) {
            $balance = (float) ($p->current_balance ?? 0);
            return [
                'id'                   => $p->id,
                'name'                 => $p->name,
                'email'                => $p->email,
                'phone'                => $p->phone,
                'receivable_balance'   => $balance > 0 ? $balance : 0,
                'payable_balance'      => $balance < 0 ? abs($balance) : 0,
                'credit_limit_enabled' => $p->credit_limit_enabled,
                'credit_limit_amount'  => $p->credit_limit_amount,
            ];
        });

        return response()->json(['success' => true, 'parties' => $parties]);
    }

    // ============================================================
    // 3. PARTY REPORT BY ITEMS
    // ============================================================
    public function partyReportByItems(Request $request)
    {
        [$from, $to] = $this->dateRange($request);

        if (!\Schema::hasTable('sales') || !\Schema::hasTable('sale_items')) {
            return response()->json(['success' => true, 'rows' => []]);
        }

        $query = DB::table('sale_items as si')
            ->join('sales as s', 's.id', '=', 'si.sale_id')
            ->join('parties as p', 'p.id', '=', 's.party_id')
            ->whereBetween('s.invoice_date', [$from, $to])
            ->select(
                'p.id as party_id',
                'p.name as party_name',
                DB::raw('SUM(si.quantity) as sale_qty'),
                DB::raw('SUM(si.amount) as sale_amount')
            )
            ->groupBy('p.id', 'p.name');

        if ($request->filled('item')) {
            $query->where('si.item_id', $request->item);
        }
        if ($request->filled('category')) {
            $query->join('items as it', 'it.id', '=', 'si.item_id')
                  ->where('it.category_id', $request->category);
        }

        $saleRows = $query->get()->keyBy('party_id');

        $purchaseRows = collect();
        if (\Schema::hasTable('purchase_items')) {
            $purchaseRows = DB::table('purchase_items as pi')
                ->join('purchases as pu', 'pu.id', '=', 'pi.purchase_id')
                ->join('parties as p', 'p.id', '=', 'pu.party_id')
                ->whereBetween('pu.bill_date', [$from, $to])
                ->select(
                    'p.id as party_id',
                    'p.name as party_name',
                    DB::raw('SUM(pi.quantity) as purchase_qty'),
                    DB::raw('SUM(pi.amount) as purchase_amount')
                )
                ->groupBy('p.id', 'p.name')
                ->get()->keyBy('party_id');
        }

        $partyIds = $saleRows->keys()->merge($purchaseRows->keys())->unique();
        $rows = $partyIds->map(function ($id) use ($saleRows, $purchaseRows) {
            $s = $saleRows->get($id);
            $p = $purchaseRows->get($id);
            return [
                'party_id'        => $id,
                'party_name'      => $s ? $s->party_name : ($p ? $p->party_name : '-'),
                'sale_qty'        => $s ? (int) $s->sale_qty : 0,
                'sale_amount'     => $this->fmt($s ? $s->sale_amount : 0),
                'purchase_qty'    => $p ? (int) $p->purchase_qty : 0,
                'purchase_amount' => $this->fmt($p ? $p->purchase_amount : 0),
            ];
        })->values();

        return response()->json(['success' => true, 'rows' => $rows]);
    }

    // ============================================================
    // 4. SALE PURCHASE BY PARTY
    // ============================================================
    public function salePurchaseByParty(Request $request)
    {
        [$from, $to] = $this->dateRange($request);
        $parties = Party::all();

        $saleMap = collect();
        if (\Schema::hasTable('sales')) {
            $saleMap = DB::table('sales')
               ->whereBetween('invoice_date', [$from, $to])
                ->select('party_id', DB::raw('SUM(total_amount) as sale_amount'))
                ->groupBy('party_id')
                ->get()->keyBy('party_id');
        }

        $purchaseMap = collect();
        if (\Schema::hasTable('purchases')) {
            $purchaseMap = DB::table('purchases')
                ->whereBetween('bill_date', [$from, $to])
                ->select('party_id', DB::raw('SUM(total_amount) as purchase_amount'))
                ->groupBy('party_id')
                ->get()->keyBy('party_id');
        }

        $rows = $parties->map(function ($p) use ($saleMap, $purchaseMap) {
            $s  = $saleMap->get($p->id);
            $pu = $purchaseMap->get($p->id);
            return [
                'party_id'        => $p->id,
                'party_name'      => $p->name,
                'sale_amount'     => $this->fmt($s ? $s->sale_amount : 0),
                'purchase_amount' => $this->fmt($pu ? $pu->purchase_amount : 0),
            ];
        })->filter(fn($r) => $r['sale_amount'] > 0 || $r['purchase_amount'] > 0)->values();

        return response()->json(['success' => true, 'rows' => $rows]);
    }

    // ============================================================
    // 5. SALE PURCHASE BY PARTY GROUP
    // ============================================================
    public function salePurchaseByPartyGroup(Request $request)
    {
        [$from, $to] = $this->dateRange($request);
        $parties = Party::all();

        $saleMap = collect();
        if (\Schema::hasTable('sales')) {
            $saleMap = DB::table('sales')
                ->whereBetween('invoice_date', [$from, $to])
                ->select('party_id', DB::raw('SUM(total_amount) as sale_amount'))
                ->groupBy('party_id')
                ->get()->keyBy('party_id');
        }

        $purchaseMap = collect();
        if (\Schema::hasTable('purchases')) {
            $purchaseMap = DB::table('purchases')
                ->whereBetween('bill_date', [$from, $to])
                ->select('party_id', DB::raw('SUM(total_amount) as purchase_amount'))
                ->groupBy('party_id')
                ->get()->keyBy('party_id');
        }

        $groups = [];
        foreach ($parties as $p) {
            $group = $p->party_group ?: 'Ungrouped';
            if (!isset($groups[$group])) {
                $groups[$group] = ['sale_amount' => 0, 'purchase_amount' => 0];
            }
            $s  = $saleMap->get($p->id);
            $pu = $purchaseMap->get($p->id);
            $groups[$group]['sale_amount']     += $s  ? (float) $s->sale_amount     : 0;
            $groups[$group]['purchase_amount'] += $pu ? (float) $pu->purchase_amount : 0;
        }

        $rows = collect($groups)->map(function ($vals, $group) {
            return [
                'party_group'     => $group,
                'sale_amount'     => $this->fmt($vals['sale_amount']),
                'purchase_amount' => $this->fmt($vals['purchase_amount']),
            ];
        })->values();

        return response()->json(['success' => true, 'rows' => $rows]);
    }
    public function saleReport(Request $request)
{
    [$from, $to] = $this->dateRange($request);
 
    // ── Guard: table must exist ─────────────────────────────
    if (!\Schema::hasTable('sales')) {
        return response()->json([
            'success'        => true,
            'transactions'   => [],
            'total_amount'   => 0,
            'total_received' => 0,
            'total_balance'  => 0,
            'growth_pct'     => 0,
        ]);
    }
 
    // ── Base query ─────────────────────────────────────────
    $query = DB::table('sales as s')
        ->leftJoin('parties as p', 'p.id', '=', 's.party_id')
        ->whereBetween('s.invoice_date', [$from, $to])
        ->select(
            's.id',
            's.bill_number',
            's.invoice_date',
            's.total_amount',
            's.payment_type',
            DB::raw("COALESCE(s.received_amount, 0) as received_paid"),
            DB::raw("COALESCE(s.total_amount, 0) - COALESCE(s.received_amount, 0) as balance_due"),
            'p.name as party_name',
            'p.phone as party_phone',
            's.status',
            's.description',
            's.order_number'
        )
        ->orderByDesc('s.invoice_date')
        ->orderByDesc('s.id');
 
    $rows = $query->get();
 
    // ── Summary ────────────────────────────────────────────
    $totalAmount   = $rows->sum('total_amount');
    $totalReceived = $rows->sum('received_paid');
    $totalBalance  = $rows->sum('balance_due');
 
    // ── Growth % vs previous period ───────────────────────
    $fromDate  = new \DateTime($from);
    $toDate    = new \DateTime($to);
    $diffDays  = $fromDate->diff($toDate)->days + 1;
    $prevFrom  = (clone $fromDate)->modify("-{$diffDays} days")->format('Y-m-d');
    $prevTo    = (clone $fromDate)->modify('-1 day')->format('Y-m-d');
 
    $prevTotal = DB::table('sales')
        ->whereBetween('invoice_date', [$prevFrom, $prevTo])
        ->sum('total_amount');
 
    $growthPct = 0;
    if ($prevTotal > 0) {
        $growthPct = round((($totalAmount - $prevTotal) / $prevTotal) * 100, 1);
    } elseif ($totalAmount > 0) {
        $growthPct = 100;
    }
 
    return response()->json([
        'success'        => true,
        'transactions'   => $rows->toArray(),
        'total_amount'   => $this->fmt($totalAmount),
        'total_received' => $this->fmt($totalReceived),
        'total_balance'  => $this->fmt($totalBalance),
        'growth_pct'     => $growthPct,
        'period'         => ['from' => $from, 'to' => $to],
    ]);
}
}