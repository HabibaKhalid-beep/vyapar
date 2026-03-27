<?php

namespace App\Http\Controllers;

use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Models\Item;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EstimateController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 10);

        $query = Estimate::with(['party', 'items', 'convertedSale']);

        // Search by bill number or party name
        if ($search) {
            $query->where('bill_number', 'LIKE', "%$search%")
                  ->orWhereHas('party', function ($q) use ($search) {
                      $q->where('name', 'LIKE', "%$search%");
                  });
        }

        // Get all estimates for summary (regardless of pagination)
        $allEstimates = $query->get();

        // Get paginated results
        $estimates = Estimate::with(['party', 'items', 'convertedSale']);
        if ($search) {
            $estimates->where('bill_number', 'LIKE', "%$search%")
                  ->orWhereHas('party', function ($q) use ($search) {
                      $q->where('name', 'LIKE', "%$search%");
                  });
        }
        $estimates = $estimates->latest('created_at')->paginate($perPage);

        return view('dashboard.sales.estimate', compact('estimates', 'allEstimates', 'search', 'perPage'));
    }

    public function update(Request $request, Estimate $estimate)
    {
        // Validation and update logic
        $data = $request->validate([
            'party_id' => 'required|exists:parties,id',
            'bill_number' => 'nullable|string|max:100',
            'estimate_date' => 'nullable|date',
            'status' => 'required|in:open,converted,pending',
            // Add other validations
        ]);

        $estimate->update($data);
        return redirect()->route('estimates.index')->with('success', 'Estimate updated successfully');
    }

    public function destroy(Estimate $estimate)
    {
        $estimate->delete();
        return response()->json(['success' => true]);
    }

    public function show(Estimate $estimate)
    {
        $estimate->load(['party', 'items']);
        return view('estimates.show', compact('estimate'));
    }

    public function create()
    {
        $parties = Party::all();
        $items = Item::all();
        return view('dashboard.sales.estimate-create', compact('parties', 'items'));
    }

    public function edit(Estimate $estimate)
    {
        $estimate->load(['party', 'items.item']);
        $parties = Party::all();
        $items = Item::all();
        return view('dashboard.sales.estimate-create', compact('estimate', 'parties', 'items'));
    }

    public function store(Request $request)
    {
        $estimateId = $request->input('estimate_id'); // Check if editing

        $data = $request->validate([
            'party_id' => 'required|exists:parties,id',
            'bill_number' => 'nullable|string|max:100',
            'estimate_date' => 'nullable|date',
            'total_qty' => 'nullable|integer|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'discount_pct' => 'nullable|numeric|min:0',
            'discount_rs' => 'nullable|numeric|min:0',
            'tax_pct' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'round_off' => 'nullable|numeric',
            'grand_total' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'image_path' => 'nullable|string|max:255',
            'status' => 'nullable|in:open,converted,pending',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.item_category' => 'nullable|string|max:255',
            'items.*.item_code' => 'nullable|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'nullable|integer|min:0',
            'items.*.unit' => 'nullable|string|max:50',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.amount' => 'nullable|numeric|min:0',
        ]);

        if ($estimateId) {
            // UPDATE existing estimate
            $estimate = Estimate::findOrFail($estimateId);

            $estimate->update([
                'party_id' => $data['party_id'],
                'bill_number' => $data['bill_number'] ?? $estimate->bill_number,
                'estimate_date' => $data['estimate_date'] ?? $estimate->estimate_date,
                'total_qty' => $data['total_qty'] ?? 0,
                'total_amount' => $data['total_amount'] ?? 0,
                'discount_pct' => $data['discount_pct'] ?? 0,
                'discount_rs' => $data['discount_rs'] ?? 0,
                'tax_pct' => $data['tax_pct'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'round_off' => $data['round_off'] ?? 0,
                'grand_total' => $data['grand_total'] ?? 0,
                'description' => $data['description'] ?? null,
                'image_path' => $data['image_path'] ?? null,
                'status' => $data['status'] ?? $estimate->status,
            ]);

            // Delete old items and create new ones
            $estimate->items()->delete();
        } else {
            // CREATE new estimate
            $estimate = Estimate::create([
                'party_id' => $data['party_id'],
                'bill_number' => $data['bill_number'] ?? null,
                'estimate_date' => $data['estimate_date'] ?? now(),
                'total_qty' => $data['total_qty'] ?? 0,
                'total_amount' => $data['total_amount'] ?? 0,
                'discount_pct' => $data['discount_pct'] ?? 0,
                'discount_rs' => $data['discount_rs'] ?? 0,
                'tax_pct' => $data['tax_pct'] ?? 0,
                'tax_amount' => $data['tax_amount'] ?? 0,
                'round_off' => $data['round_off'] ?? 0,
                'grand_total' => $data['grand_total'] ?? 0,
                'description' => $data['description'] ?? null,
                'image_path' => $data['image_path'] ?? null,
                'status' => $data['status'] ?? 'open',
                'converted_sale_id' => null,
            ]);

            // Auto-generate bill number based on the estimate ID if not provided
            if (empty($estimate->bill_number)) {
                $estimate->bill_number = 'EST-' . str_pad($estimate->id, 4, '0', STR_PAD_LEFT);
                $estimate->save();
            }
        }

        // Create items
        foreach ($data['items'] as $item) {
            $estimate->items()->create([
                'item_id' => $item['item_id'],
                'item_category' => $item['item_category'] ?? null,
                'item_code' => $item['item_code'] ?? null,
                'item_description' => $item['item_description'] ?? null,
                'quantity' => $item['quantity'] ?? 0,
                'unit' => $item['unit'] ?? null,
                'unit_price' => $item['unit_price'] ?? 0,
                'discount' => $item['discount'] ?? 0,
                'amount' => $item['amount'] ?? 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'estimate_id' => $estimate->id,
            'bill_number' => $estimate->bill_number,
            'message' => $estimateId ? 'Estimate updated successfully!' : 'Estimate created successfully!',
        ]);
    }

    public function print(Estimate $estimate)
    {
        $estimate->load(['party', 'items.item']);
        return view('dashboard.sales.estimate-print', compact('estimate'));
    }

    public function preview(Estimate $estimate)
    {
        $estimate->load(['party', 'items.item']);
        return view('dashboard.sales.estimate-preview', compact('estimate'));
    }

    public function pdf(Estimate $estimate)
    {
        $estimate->load(['party', 'items.item']);
            return view('dashboard.sales.estimate-pdf', compact('estimate'));
    }

    public function convert(Estimate $estimate)
    {
        try {
            $estimate->update(['status' => 'converted']);
            return response()->json(['success' => true, 'message' => 'Estimate converted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
