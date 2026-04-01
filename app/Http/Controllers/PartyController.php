<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Party;
use App\Models\Sale;
use App\Models\Transaction;


class PartyController extends Controller
{
    // Display all parties
    public function index()
    {
        $parties = Party::with('sales')->latest()->get();
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


        $party->load('sales');

        return response()->json([
            'success' => true,
            'party' => $party
        ]);
 
        
    }

    // Show single party
  public function show($id)
{
    // Eager load transactions
    $party = Party::with(['transactions', 'sales'])->findOrFail($id);

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
        'total_balance' => number_format((float) $party->current_balance, 2),
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

    $party->load(['transactions', 'sales']);

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
        'total_balance' => number_format((float) $party->current_balance, 2),
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
    $party->loadMissing(['transactions', 'sales']);

    $salesTransactions = Sale::query()
        ->where('party_id', $party->id)
        ->get()
        ->map(function ($sale) {
            $typeLabel = match ($sale->type) {
                'invoice' => 'Sale',
                'estimate' => 'Estimate',
                'sale_order' => 'Sale Order',
                'proforma' => 'Proforma Invoice',
                'delivery_challan' => 'Delivery Challan',
                'sale_return' => 'Credit Note',
                'pos' => 'POS',
                default => ucfirst((string) $sale->type),
            };

            $date = $sale->invoice_date ?? $sale->order_date ?? $sale->due_date ?? $sale->created_at;

            return [
                'id' => $sale->id,
                'type' => $typeLabel,
                'number' => $sale->bill_number ?: (string) $sale->id,
                'date' => optional($date)->format('d/m/Y'),
                'total' => number_format((float) ($sale->grand_total ?? $sale->total_amount ?? 0), 2),
                'balance' => number_format((float) ($sale->balance ?? 0), 2),
                'status' => (string) ($sale->status ?? ''),
                'sort_date' => optional($date)->timestamp ?? $sale->created_at?->timestamp ?? 0,
            ];
        });

    $openingBalanceTransactions = $party->transactions->map(function ($txn) {
        $typeLabel = $txn->type === 'pay'
            ? 'Payable Opening Balance'
            : 'Receivable Opening Balance';

        return [
            'id' => 'opening-' . $txn->id,
            'type' => $typeLabel,
            'number' => $txn->number ?: '-',
            'date' => optional($txn->date)->format('d/m/Y'),
            'total' => number_format((float) ($txn->total ?? 0), 2),
            'balance' => number_format((float) ($txn->balance ?? 0), 2),
            'status' => (string) ($txn->type ?? ''),
            'sort_date' => optional($txn->date)->timestamp ?? 0,
        ];
    });

    $transactions = $salesTransactions
        ->concat($openingBalanceTransactions)
        ->sortByDesc('sort_date')
        ->values()
        ->map(function ($transaction) {
            unset($transaction['sort_date']);
            return $transaction;
        });

    return response()->json([
        'success'        => true,
        'transactions'   => $transactions,
        'party_name'     => $party->name,
        'total_balance'  => number_format((float) $party->current_balance, 2),
    ]);
}
}
