<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseExpenseController extends Controller
{
    //

    public function purchaseExpenses()
    {
        return view('dashboard.purchases.purchase-bill');
    }

    public function paymentOut()
    {
        return view('dashboard.purchases.payement-out');
    }

}
