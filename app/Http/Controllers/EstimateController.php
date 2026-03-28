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

        $baseQuery = Sale::with(['items'])
            ->where('type', 'estimate')
            ->orderByDesc('created_at');

        $allEstimates = (clone $baseQuery)->get();

        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search) {
                $query->where('bill_number', 'like', "%{$search}%")
                    ->orWhere('party_name', 'like', "%{$search}%");
            });
        }

        $estimates = $baseQuery->get();

        $convertedInvoices = Sale::where('type', 'invoice')
            ->whereNotNull('reference_id')
            ->whereIn('reference_id', $allEstimates->pluck('id'))
            ->pluck('bill_number', 'reference_id');

        return view('dashboard.sales.estimate', compact('estimates', 'allEstimates', 'search', 'convertedInvoices'));
    }

    public function create()
    {
        $items = Item::orderBy('name')->get();
        $parties = Party::orderBy('name')->get();
        $nextSaleId = (Sale::max('id') ?? 0) + 1;
        $nextInvoiceNumber = 'EST-' . str_pad($nextSaleId, 4, '0', STR_PAD_LEFT);

        return view('dashboard.sales.estimate-create', compact('items', 'parties', 'nextInvoiceNumber'));
    }
}
