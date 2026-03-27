<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $bankAccounts = BankAccount::orderByDesc('created_at')->get();
        return view('dashboard.accounts.bank', compact('bankAccounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'display_name' => 'required|string|max:255',
            'opening_balance' => 'nullable|numeric',
            'as_of_date' => 'nullable|date',
            'account_number' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'print_on_invoice' => 'nullable|boolean',
        ]);

        $data['print_on_invoice'] = $request->has('print_on_invoice');

        $bankAccount = BankAccount::create($data);

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Bank account added successfully.', 'bank' => $bankAccount]);
        }

        return redirect()->route('bank-accounts')->with('success', 'Bank account added successfully.');
    }

    public function show(BankAccount $bankAccount)
    {
        // Return JSON for viewing/editing via AJAX.
        if (request()->wantsJson()) {
            return response()->json($bankAccount);
        }

        // Fallback: redirect to listing if accessed via browser.
        return redirect()->route('bank-accounts');
    }

    public function edit(BankAccount $bankAccount)
    {
        // Return JSON for JS-powered edit form.
        return response()->json($bankAccount);
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $data = $request->validate([
            'display_name' => 'required|string|max:255',
            'opening_balance' => 'nullable|numeric',
            'as_of_date' => 'nullable|date',
            'account_number' => 'nullable|string|max:255',
            'swift_code' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_holder_name' => 'nullable|string|max:255',
            'print_on_invoice' => 'nullable|boolean',
        ]);

        $data['print_on_invoice'] = $request->has('print_on_invoice');

        $bankAccount->update($data);

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Bank account updated successfully.', 'bank' => $bankAccount]);
        }

        return redirect()->route('bank-accounts')->with('success', 'Bank account updated successfully.');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Bank account deleted successfully.']);
        }

        return redirect()->route('bank-accounts')->with('success', 'Bank account deleted successfully.');
    }

    public function cashInHand()
    {
        // For simplicity, we can treat "Cash in Hand" as a special bank account with a fixed ID (e.g., 0).
        // Alternatively, you could have a separate model/table for cash transactions.


        return view('dashboard.accounts.cash-hand');
    }
}

