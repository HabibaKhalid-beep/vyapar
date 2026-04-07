<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Party;
use App\Models\Transaction;
use App\Models\BankAccount;
use Illuminate\Support\Facades\DB;

class PaymentInController extends Controller
{
    public function index()
    {
        return view('dashboard.sales.payement-in', [
            'parties'      => Party::all(),
            'bankAccounts' => BankAccount::active()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'party_id'                   => 'required|exists:parties,id',
            'payments'                   => 'required|array|min:1',
            'payments.*.type'            => 'required|string',
            'payments.*.amount'          => 'required|numeric|min:1',
            'payments.*.bank_account_id' => 'nullable|exists:bank_accounts,id',
            'date'                       => 'required|date',
            'reference_no'               => 'nullable|string',
            'receipt_no'                 => 'nullable|string',
        ]);

        try {
            $party = Party::findOrFail($request->party_id);

            DB::transaction(function () use ($request, $party) {
                foreach ($request->payments as $pay) {

                   
                    Transaction::create([
                        'party_id'        => $party->id,
                        'type'            => 'receive',
                        'payment_type'    => $pay['type'],
                        'amount'          => $pay['amount'],
                        'date'            => $request->date,
                        'reference_no'    => $request->reference_no ?? null,
                        'receipt_no'      => $request->receipt_no ?? null,
                        'bank_account_id' => $pay['bank_account_id'] ?? null,
                    ]);

                  
                    $party->balance -= $pay['amount'];
                    $party->save();

                    
                    if (!empty($pay['bank_account_id'])) {
                        $bank = BankAccount::findOrFail($pay['bank_account_id']);
                        $bank->balance += $pay['amount'];
                        $bank->save();
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Payment record ho gaya!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
