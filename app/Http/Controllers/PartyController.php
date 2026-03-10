<?php

namespace App\Http\Controllers;

class PartyController extends Controller
{
    public function index()
    {
        return view('parties.index');
    }
}
