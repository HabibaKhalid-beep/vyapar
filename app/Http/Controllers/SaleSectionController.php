<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
 use App\Models\Party;


class SaleSectionController extends Controller
{
    //

 

public function paymentIn()
{
    // Fetch all parties
    $parties = Party::all();

    // Return the view with $parties
    return view('dashboard.sales.payement-in', compact('parties'));
}
    

    public function proformaInvoice()
    {
        return view('dashboard.sales.perfoma-invoice');
    }


 




}
