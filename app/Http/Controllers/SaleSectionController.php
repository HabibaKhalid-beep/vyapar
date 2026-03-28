<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SaleSectionController extends Controller
{
    //

    public function paymentIn()
    {
        return view('dashboard.sales.payement-in');
    }

    public function proformaInvoice()
    {
        return view('dashboard.sales.perfoma-invoice');
    }

    public function saleReturn()
    {
        return view('dashboard.sales.sale-return');
    }

    public function deliveryChallan()
    {
        return view('dashboard.sales.delivery-challan');
    }



}
