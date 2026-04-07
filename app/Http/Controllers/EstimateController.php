<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Party;
use App\Models\Sale;
use Illuminate\Http\Request;

class EstimateController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $baseQuery = Sale::with(['items', 'party'])
            ->where('type', 'estimate')
            ->orderByDesc('created_at');

        $allEstimates = (clone $baseQuery)->get();

        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('bill_number', 'like', "%{$search}%")
                    ->orWhereHas('party', function ($partyQuery) use ($search) {
                        $partyQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $estimates = $baseQuery->get();

        $convertedInvoices = Sale::where('type', 'invoice')
            ->whereNotNull('reference_id')
            ->whereIn('reference_id', $allEstimates->pluck('id'))
            ->pluck('bill_number', 'reference_id');

        return view('dashboard.sales.estimate', compact('estimates', 'allEstimates', 'search', 'convertedInvoices'));
    }

    public function create(Request $request)
    {
        $items = Item::active()->orderBy('name')->get();
        $parties = Party::orderBy('name')->get();
        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = 'EST-' . str_pad($nextSaleId, 4, '0', STR_PAD_LEFT);

        $estimate = null;
        $prefilledEstimateData = null;

        if ($request->filled('edit_sale_id')) {
            $estimate = Sale::with(['items.item', 'party', 'payments'])->where('type', 'estimate')->findOrFail($request->integer('edit_sale_id'));
        }

        if ($request->filled('duplicate_sale_id')) {
            $sourceEstimate = Sale::with(['items.item', 'party', 'payments'])->where('type', 'estimate')->findOrFail($request->integer('duplicate_sale_id'));
            $prefilledEstimateData = $sourceEstimate->toArray();
            $prefilledEstimateData['bill_number'] = $nextInvoiceNumber;
            $prefilledEstimateData['invoice_date'] = now()->toDateString();
            $prefilledEstimateData['due_date'] = $sourceEstimate->due_date?->format('Y-m-d') ?: now()->toDateString();
            $prefilledEstimateData['received_amount'] = 0;
            $prefilledEstimateData['balance'] = $sourceEstimate->grand_total ?? $sourceEstimate->total_amount ?? 0;
            $prefilledEstimateData['payments'] = [];
        }

        return view('dashboard.sales.estimate-create', compact('items', 'parties', 'nextInvoiceNumber', 'estimate', 'prefilledEstimateData'));
    }
}
