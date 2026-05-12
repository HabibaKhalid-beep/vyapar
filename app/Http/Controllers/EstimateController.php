<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\BankAccount;
use App\Models\Broker;
use App\Models\Item;
use App\Models\Party;
use App\Models\PartyGroup;
use App\Models\Sale;
use App\Support\TransactionNumberPrefix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $categories = Category::orderBy('name')->get();
        $parties = Party::orderBy('name')->get();
        $partyGroups = PartyGroup::orderBy('name')->get();
        $bankAccounts = BankAccount::active()->orderBy('display_name')->get();
        $brokers = Broker::orderBy('name')->get();

        // Get next estimate number as a plain integer
        $lastEstimate = Sale::where('type', 'estimate')->orderByDesc('id')->first();
        $nextEstimateNumber = $lastEstimate
            ? ((int) preg_replace('/[^0-9]/', '', $lastEstimate->bill_number)) + 1
            : 1;

        // Format for display: "Estimate #1"
        $displayEstimateNumber = 'Estimate #' . $nextEstimateNumber;

        // Formatted version for storage (e.g., EST-0001)
        $nextInvoiceNumber = TransactionNumberPrefix::format('estimate', $nextEstimateNumber);

        $estimate = null;
        $prefilledEstimateData = null;

        if ($request->filled('edit_sale_id')) {
            $estimate = Sale::with(['items.item', 'party', 'payments'])
                ->where('type', 'estimate')
                ->findOrFail($request->integer('edit_sale_id'));
        }

        if ($request->filled('duplicate_sale_id')) {
            $sourceEstimate = Sale::with(['items.item', 'party', 'payments'])
                ->where('type', 'estimate')
                ->findOrFail($request->integer('duplicate_sale_id'));
            $prefilledEstimateData = $sourceEstimate->toArray();
            $prefilledEstimateData['bill_number'] = $nextInvoiceNumber;
            $prefilledEstimateData['invoice_date'] = now()->toDateString();
            $prefilledEstimateData['due_date'] = $sourceEstimate->due_date?->format('Y-m-d') ?: now()->toDateString();
            $prefilledEstimateData['received_amount'] = 0;
            $prefilledEstimateData['balance'] = $sourceEstimate->grand_total ?? $sourceEstimate->total_amount ?? 0;
            $prefilledEstimateData['payments'] = [];
        }

        $docType = 'estimate';
return view('dashboard.sales.estimate-create', compact(
    'items', 'categories', 'parties', 'partyGroups', 'bankAccounts',
    'brokers', 'nextInvoiceNumber', 'displayEstimateNumber', 'estimate', 'prefilledEstimateData', 'docType'
));
    }

    public function store(Request $request)
    {
        if (is_string($request->input('items'))) {
            $decoded = json_decode($request->input('items'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request->merge(['items' => $decoded]);
            }
        }

        $data = $request->validate([
            'party_id'                 => 'nullable|exists:parties,id',
            'broker_id'                => 'nullable|exists:brokers,id',
            'bill_number'              => 'required|string|max:100',
            'invoice_date'             => 'nullable|date',
            'due_date'                 => 'nullable|date',
            'phone'                    => 'nullable|string|max:50',
            'billing_address'          => 'nullable|string|max:1000',
            'discount_pct'             => 'nullable|numeric|min:0',
            'discount_rs'              => 'nullable|numeric|min:0',
            'tax_pct'                  => 'nullable|numeric|min:0',
            'tax_amount'               => 'nullable|numeric|min:0',
            'round_off'                => 'nullable|numeric',
            'grand_total'              => 'nullable|numeric|min:0',
            'total_amount'             => 'nullable|numeric|min:0',
            'total_qty'                => 'nullable|integer|min:0',
            'description'              => 'nullable|string',
            'status'                   => 'nullable|string|max:50',
            'items'                    => 'required|array|min:1',
            'items.*.item_name'        => 'nullable|string|max:255',
            'items.*.item_category'    => 'nullable|string|max:255',
            'items.*.item_code'        => 'nullable|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity'         => 'nullable|numeric|min:0',
            'items.*.unit'             => 'nullable|string|max:50',
            'items.*.unit_price'       => 'nullable|numeric|min:0',
            'items.*.discount'         => 'nullable|numeric|min:0',
            'items.*.amount'           => 'nullable|numeric|min:0',
        ]);

        $sale = DB::transaction(function () use ($data) {
            $sale = Sale::create([
                'type'            => 'estimate',
                'party_id'        => $data['party_id'] ?? null,
                'broker_id'       => $data['broker_id'] ?? null,
                'bill_number'     => $data['bill_number'],
                'invoice_date'    => $data['invoice_date'] ?? now()->toDateString(),
                'due_date'        => $data['due_date'] ?? now()->toDateString(),
                'phone'           => $data['phone'] ?? null,
                'billing_address' => $data['billing_address'] ?? null,
                'total_qty'       => $data['total_qty'] ?? 0,
                'total_amount'    => $data['total_amount'] ?? 0,
                'discount_pct'    => $data['discount_pct'] ?? 0,
                'discount_rs'     => $data['discount_rs'] ?? 0,
                'tax_pct'         => $data['tax_pct'] ?? 0,
                'tax_amount'      => $data['tax_amount'] ?? 0,
                'round_off'       => $data['round_off'] ?? 0,
                'grand_total'     => $data['grand_total'] ?? 0,
                'received_amount' => 0,
                'balance'         => $data['grand_total'] ?? 0,
                'status'          => $data['status'] ?? 'open',
                'description'     => $data['description'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $sale->items()->create([
                    'item_name'        => isset($item['item_name']) ? (string) $item['item_name'] : null,
                    'item_category'    => $item['item_category'] ?? null,
                    'item_code'        => $item['item_code'] ?? null,
                    'item_description' => $item['item_description'] ?? null,
                    'quantity'         => $item['quantity'] ?? 0,
                    'unit'             => $item['unit'] ?? null,
                    'unit_price'       => $item['unit_price'] ?? 0,
                    'discount'         => $item['discount'] ?? 0,
                    'amount'           => $item['amount'] ?? 0,
                ]);
            }

            return $sale;
        });

        return response()->json([
            'success'      => true,
            'sale_id'      => $sale->id,
            'bill_number'  => $sale->bill_number,
            'redirect_url' => route('estimates.index'),
        ]);
    }

    public function destroy(Sale $sale)
    {
        abort_unless($sale->type === 'estimate', 404);
        $sale->items()->delete();
        $sale->payments()->delete();
        $sale->delete();

        return response()->json(['success' => true]);
    }
}