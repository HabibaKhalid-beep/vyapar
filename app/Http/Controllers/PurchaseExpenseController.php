<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseExpenseController extends Controller
{
    //



    public function paymentOut()
    {
        return view('dashboard.purchases.payement-out');
    }



    public function expense()
    {
        return view('dashboard.purchases.expense');
    }

    public function purchaseReturn()
    {
        return view('dashboard.purchases.purchase-return');
    }


}
