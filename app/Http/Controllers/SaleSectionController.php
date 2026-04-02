<?php

namespace App\Http\Controllers;
use App\Models\BankAccount;

use Illuminate\Http\Request;
 use App\Models\Party;


class SaleSectionController extends Controller
{
    //

 

  public function paymentIn()
{
    $parties = Party::all();
    $bankAccounts = BankAccount::all(); 

    return view('dashboard.sales.payement-in', compact('parties', 'bankAccounts')); // ✅
}
    

    public function proformaInvoice()
    {
        return view('dashboard.sales.perfoma-invoice');
        
    }


 




}
