<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Party;
use App\Models\Transaction;


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
      

// Party create ke baad

        $party = Party::create($data);
        $transaction = Transaction::create([
    'party_id' => $party->id,
    'type'     => $request->input('transaction_type'), // receive/pay
    'number'   => 'TXN' . time(), 
    'date'     => $request->input('as_of_date') ?? now(),
    'total'    => $request->input('opening_balance') ?? 0,
    'balance'  => $request->input('opening_balance') ?? 0,
    'status'   => $request->input('transaction_type') ?? 'unpaid',
]);


        return response()->json([
            'success' => true,
            'party' => $party
        ]);
 
        
    }

    // Show single party
  public function show($id)
{
    // Eager load transactions
    $party = Party::with('transactions')->findOrFail($id);

    // Format transactions
    $transactions = $party->transactions
        ->sortByDesc('date')
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
        'success'       => true,
        'party'         => $party,
        'transactions'  => $transactions,
        'total_balance' => number_format($party->transactions->sum('balance'), 2),
    ]);
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
        'opening_balance' => 'sometimes|nullable|numeric',
        'as_of_date' => 'sometimes|nullable|date',
        'credit_limit_enabled' => 'sometimes|nullable|boolean',
        'custom_fields' => 'sometimes|nullable|array',
        'transaction_type' => 'sometimes|nullable|in:receive,pay',
        'party_type' => 'sometimes|nullable|in:customer,supplier'
    ]);

    $party->update($data);

    // ✅ Transaction update
    $transaction = $party->transactions()->latest()->first(); // last transaction
    if ($transaction) {
        $transaction->update([
            'type'    => $request->input('transaction_type', $transaction->type),
            'date'    => $request->input('as_of_date', $transaction->date),
            'total'   => $request->input('opening_balance', $transaction->total),
            'balance' => $request->input('opening_balance', $transaction->balance),
            'status'  => $request->input('transaction_type', $transaction->status),
        ]);
    }

    $party->load('transactions');

    $transactions = $party->transactions
        ->sortByDesc('date')
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
        'success'       => true,
        'party'         => $party,
        'transactions'  => $transactions,
        'total_balance' => number_format($party->transactions->sum('balance'), 2),
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
    // Pehle transactions ko fetch karo, descending order me
    $transactions = $party->transactions
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

    // Total balance calculate karna using collection
    $total_balance = $party->transactions->sum('balance');

    return response()->json([
        'success'        => true,
        'transactions'   => $transactions,
        'party_name'     => $party->name,
        'total_balance'  => number_format($total_balance, 2),
    ]);
}
}