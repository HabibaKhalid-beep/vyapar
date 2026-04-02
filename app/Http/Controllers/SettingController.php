<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    //

    public function general()
    {
        return view('dashboard.settings.general');
    }

    public function transactions()
    {
        return view('dashboard.settings.transactions');
    }

    public function taxes()
    {
        return view('dashboard.settings.taxes');
    }

    public function items()
    {
        return view('dashboard.settings.items');
    }

    public function parties()
    {
        return view('dashboard.settings.parties');
    }

    public function transactionMessages()
    {
        return view('dashboard.settings.transaction-message');
    }

    public function printLayout()
    {
        return view('dashboard.settings.print');
    }


}
