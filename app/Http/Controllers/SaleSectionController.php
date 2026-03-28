<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SaleSectionController extends Controller
{
    //

    public function paymentIn()
    {
        return redirect()->route('sale.index.type', ['type' => 'pos']);
    }

    public function proformaInvoice()
    {
        return redirect()->route('sale.index.type', ['type' => 'proforma']);
    }

    public function saleReturn()
    {
        return redirect()->route('sale.index.type', ['type' => 'sale_return']);
    }

    public function deliveryChallan()
    {
        return redirect()->route('sale.index.type', ['type' => 'delivery_challan']);
    }



}
