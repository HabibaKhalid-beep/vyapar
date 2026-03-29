<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Party;
use App\Models\Transaction;
use App\Models\BankAccount;

class PaymentInController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'party_id' => 'required|exists:parties,id',
            'payments' => 'required|array|min:1',
            'payments.*.type' => 'required|string',
            'payments.*.amount' => 'required|numeric|min:1',
            'date' => 'required|date',
        ]);

        $party = Party::find($request->party_id);

        foreach($request->payments as $pay) {
            Transaction::create([
                'party_id' => $party->id,
                'type' => $pay['type'], // cash / remote / bank
                'amount' => $pay['amount'],
                'date' => $request->date,
                'reference_no' => $request->reference_no ?? null,
                'receipt_no' => $request->receipt_no ?? null,
            ]);

            $party->opening_balance -= $pay['amount'];
            $party->save();

            $bankAccount = BankAccount::where('type', $pay['type'])->first();
            if($bankAccount) {
                $bankAccount->balance += $pay['amount'];
                $bankAccount->save();
            }
        }

        return response()->json(['success' => true]);
    }
}