<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankAccount;
use App\Models\Item;
use App\Models\Party;

class ExpenseCreateController extends Controller
{
    //

        public function expense()
    {
        return view('dashboard.expense.expense');
    }

    public function createExpense()
    {
         $bankAccounts = BankAccount::orderBy('display_name')->get();
        $items = Item::orderBy('name')->get();
        $parties = Party::orderBy('name')->get();

        return view('dashboard.expense.create-expense', compact('bankAccounts', 'items', 'parties'));
    }

}
