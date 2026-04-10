<?php

namespace App\Http\Controllers;

class InvoiceController extends Controller
{
    public function index()
    {
        $reactCss = collect(glob(public_path('react-invoice/assets/index-*.css')))
            ->map(fn ($path) => asset('react-invoice/assets/' . basename($path)))
            ->first();

        $reactJs = collect(glob(public_path('react-invoice/assets/index-*.js')))
            ->map(fn ($path) => asset('react-invoice/assets/' . basename($path)))
            ->first();

        return view('invoice.index', compact('reactCss', 'reactJs'));
    }

    public function print()
    {
        return view('invoice.print');
    }
}
