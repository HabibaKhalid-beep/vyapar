<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Party;

class PartyController extends Controller
{
    // Display all parties
    public function index()
    {
        $parties = Party::latest()->get();
        return view('parties.index', compact('parties'));
    }

    // Store a new party
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'billing_address' => 'nullable|string',
            'shipping_address' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
            'as_of_date' => 'nullable|date',
            'credit_limit_enabled' => 'nullable|boolean',
            'custom_fields' => 'nullable|array',
            'transaction_type' => 'nullable|in:receive,pay',
            'party_type' => 'nullable|in:customer,supplier'
        ]);

        $party = Party::create($data);

        return response()->json([
            'success' => true,
            'party' => $party
        ]);
    }

    // Show single party
    public function show($id)
    {
        $party = Party::findOrFail($id);
        return response()->json($party);
    }

    // Update existing party
   public function update(Request $request, $id)
{
    $party = Party::findOrFail($id);

    $data = $request->validate([
        'name' => 'sometimes|string|max:255',
        'phone' => 'sometimes|nullable|string|max:20',
        'email' => 'sometimes|nullable|email|max:255',
        'billing_address' => 'sometimes|nullable|string',
        'shipping_address' => 'sometimes|nullable|string',
        'opening_balance' => 'sometimes|nullable|numeric',          // ✅ FIX
        'as_of_date' => 'sometimes|nullable|date',
        'credit_limit_enabled' => 'sometimes|nullable|boolean',
        'custom_fields' => 'sometimes|nullable|array',
        'transaction_type' => 'sometimes|nullable|in:receive,pay',
        'party_type' => 'sometimes|nullable|in:customer,supplier'
    ]);

    $party->update($data);

    return response()->json([
        'success' => true,
        'party' => $party->fresh()
    ]);
}
    // Delete party
    public function destroy($id)
    {
        $party = Party::findOrFail($id);
        $party->delete();

        return response()->json(['success' => true]);
    }

     public function transactions(Party $party)
    {
        $transactions = $party->transactions()
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($txn) {
                return [
                    'id'      => $txn->id,
                    'type'    => $txn->type,
                    'number'  => $txn->number,
                    'date'    => $txn->date->format('d/m/Y'),
                    'total'   => number_format($txn->total, 2),
                    'balance' => number_format($txn->balance, 2),
                    'status'  => $txn->status,
                ];
            });

        return response()->json([
            'success'      => true,
            'transactions' => $transactions,
            'party_name'   => $party->name,
            'total_balance' => number_format($party->transactions->sum('balance'), 2),
        ]);
    }
}
