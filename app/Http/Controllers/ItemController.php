<?php

namespace App\Http\Controllers;

class ItemController extends Controller
{
    public function index()
    {
        return view('items.index');
    }
}
