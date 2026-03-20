<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Party;

class PartyController extends Controller

{

  public function index()
{
    $parties = Party::latest()->get();

    return view('parties.index', compact('parties'));
}
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
              'transaction_type' => 'nullable|in:receive,pay'
              
        ]);

        $party = Party::create($data);

        return response()->json([
            'success' => true,
            'party' => $party
        ]);
    }

    public function show($id)
{
    $party = Party::findOrFail($id);
    return response()->json($party);

}

//update method

public function update(Request $request, $id)
{
    $party = Party::findOrFail($id);

    $data = $request->all();

    $party->update([
        'name' => $data['name'] ?? $party->name,
        'phone' => $data['phone'] ?? $party->phone,
        'email' => $data['email'] ?? $party->email,
        'billing_address' => $data['billing_address'] ?? $party->billing_address,
        'shipping_address' => $data['shipping_address'] ?? $party->shipping_address,
        'opening_balance' => $data['opening_balance'] ?? $party->opening_balance,
        'as_of_date' => $data['as_of_date'] ?? $party->as_of_date,
        'credit_limit_enabled' => $data['credit_limit_enabled'] ?? $party->credit_limit_enabled,
        'custom_fields' => $data['custom_fields'] ?? $party->custom_fields,
        'transaction_type' => $data['transaction_type'] ?? $party->transaction_type, // ✅ yeh sahi jagah hai
    ]);

    return response()->json(['success' => true, 'party' => $party]);
}
public function destroy($id)
{
   $party = Party::findOrFail($id);
   $party->delete();

   return response()->json(['success'=>true]);
}
}
