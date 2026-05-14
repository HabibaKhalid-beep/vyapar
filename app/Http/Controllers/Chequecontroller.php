<?php

namespace App\Http\Controllers;

use App\Models\ChequeTransaction;
use App\Models\CashTransaction;
use Illuminate\Http\Request;

class ChequeController extends Controller
{
    public function index()
    {
        $transactions = ChequeTransaction::latest('date')->paginate(20);

        $totalIn      = ChequeTransaction::where('type', 'CHEQUE_IN')->sum('amount');
        $totalOut     = ChequeTransaction::where('type', 'CHEQUE_OUT')->sum('amount');
        $totalBalance = $totalIn - $totalOut;
        $pendingCount = ChequeTransaction::where('status', 'pending')->count();

        return view('dashboard.accounts.cheques', compact(
            'transactions',
            'totalBalance',
            'totalIn',
            'totalOut',
            'pendingCount'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'             => 'required|in:CHEQUE_IN,CHEQUE_OUT',
            'name'             => 'required|string|max:255',
            'cheque_number'    => 'nullable|string|max:50',
            'amount'           => 'required|numeric|min:0',
            'date'             => 'required|date',
            'status'           => 'required|in:pending,cleared,bounced',
            'notes'            => 'nullable|string',
            'transfer_to_cash' => 'nullable|boolean',
        ]);

        ChequeTransaction::create([
            'type'          => $validated['type'],
            'name'          => $validated['name'],
            'cheque_number' => $validated['cheque_number'] ?? null,
            'amount'        => $validated['amount'],
            'date'          => $validated['date'],
            'status'        => $validated['status'],
            'notes'         => $validated['notes'] ?? null,
        ]);

        if ($request->boolean('transfer_to_cash')) {
            CashTransaction::create([
                'type'   => $validated['type'] === 'CHEQUE_IN' ? 'CASH IN' : 'CASH OUT',
                'name'   => 'Cheque transfer: ' . $validated['name'],
                'amount' => $validated['amount'],
                'date'   => $validated['date'],
            ]);
        }

        return redirect()->route('cheques.index')
            ->with('success', 'Cheque transaction saved successfully.');
    }

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'adjust_type' => 'required|in:add,reduce',
            'date'        => 'required|date',
            'reason'      => 'nullable|string|max:255',
        ]);

        ChequeTransaction::create([
            'type'   => $validated['adjust_type'] === 'add' ? 'CHEQUE_IN' : 'CHEQUE_OUT',
            'name'   => $validated['reason'] ?? 'Balance adjustment',
            'amount' => $validated['amount'],
            'date'   => $validated['date'],
            'status' => 'cleared',
        ]);

        return redirect()->route('cheques.index')
            ->with('success', 'Cheque balance adjusted successfully.');
    }
    public function update(Request $request, ChequeTransaction $cheque)
{
    $request->validate([
        'status' => 'required|in:pending,cleared,bounced',
    ]);

    $cheque->update(['status' => $request->status]);

    return redirect()->route('cheques.index')
        ->with('success', 'Cheque status updated successfully.');
}
}