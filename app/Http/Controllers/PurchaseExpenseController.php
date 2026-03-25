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

    public  function purchaseOrder()
    {
        return view('dashboard.purchases.purchase-order');
    }

    public function expense()
    {
        return view('dashboard.purchases.expense');
    }
    

}
