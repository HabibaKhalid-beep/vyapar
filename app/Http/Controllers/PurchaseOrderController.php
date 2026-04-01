<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankAccount;
use App\Models\Item;
use App\Models\Party;

class PurchaseOrderController extends Controller
{
    //

        public  function purchaseOrder()
    {
        return view('dashboard.purchases.purchase-order');
    }

    public function create()
    {
         $bankAccounts = BankAccount::orderBy('display_name')->get();
         $items = Item::orderBy('name')->get();
         $parties = Party::orderBy('name')->get();
        return view('dashboard.purchases.create-purchase-order', compact('bankAccounts', 'items', 'parties'));
    }
    

}
