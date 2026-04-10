<?php

namespace App\Http\Controllers;

use App\Models\PaymentIn;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $reactCss = collect(glob(public_path('react-invoice/assets/index-*.css')))
            ->sortByDesc(fn ($path) => filemtime($path))
            ->map(fn ($path) => asset('react-invoice/assets/' . basename($path)))
            ->first();

        $reactJs = collect(glob(public_path('react-invoice/assets/index-*.js')))
            ->sortByDesc(fn ($path) => filemtime($path))
            ->map(fn ($path) => asset('react-invoice/assets/' . basename($path)))
            ->first();

        $paymentIn = null;
        $allPaymentIns = [];

        if ($request->filled('payment_in')) {
            $paymentInRecord = PaymentIn::with(['party', 'bankAccount'])
                ->find($request->integer('payment_in'));

            if ($paymentInRecord) {
                // Transform PaymentIn into invoice format
                $paymentIn = [
                    'id' => $paymentInRecord->id,
                    'invoice_number' => $paymentInRecord->receipt_no ?? $paymentInRecord->id,
                    'date' => $paymentInRecord->date,
                    'party' => $paymentInRecord->party,
                    'bank_account' => $paymentInRecord->bankAccount,
                    'amount' => $paymentInRecord->amount,
                    'payment_type' => $paymentInRecord->payment_type,
                    'reference_no' => $paymentInRecord->reference_no,
                    'receipt_no' => $paymentInRecord->receipt_no,
                    'description' => $paymentInRecord->description,
                    'items' => [
                        [
                            'name' => $paymentInRecord->payment_type . ' Payment',
                            'quantity' => 1,
                            'price' => $paymentInRecord->amount,
                            'amount' => $paymentInRecord->amount,
                        ]
                    ],
                ];
            }
        }

        // Get all payment invoices
        $allPaymentIns = PaymentIn::with(['party', 'bankAccount'])->latest()->get();

        return view('invoice.index', compact('reactCss', 'reactJs', 'paymentIn', 'allPaymentIns'));
    }

    public function print()
    {
        return view('invoice.print');
    }

    public function paymentIn(Request $request)
    {
        $reactCss = collect(glob(public_path('react-invoice/assets/index-*.css')))
            ->sortByDesc(fn ($path) => filemtime($path))
            ->map(fn ($path) => asset('react-invoice/assets/' . basename($path)))
            ->first();

        $reactJs = collect(glob(public_path('react-invoice/assets/index-*.js')))
            ->sortByDesc(fn ($path) => filemtime($path))
            ->map(fn ($path) => asset('react-invoice/assets/' . basename($path)))
            ->first();

        $paymentIn = null;
        $allPaymentIns = [];

        if ($request->filled('payment_in')) {
            $paymentInRecord = PaymentIn::with(['party', 'bankAccount'])
                ->find($request->integer('payment_in'));

            if ($paymentInRecord) {
                // Transform PaymentIn into invoice format
                $paymentIn = [
                    'id' => $paymentInRecord->id,
                    'invoice_number' => $paymentInRecord->receipt_no ?? $paymentInRecord->id,
                    'date' => $paymentInRecord->date,
                    'party' => $paymentInRecord->party,
                    'bank_account' => $paymentInRecord->bankAccount,
                    'amount' => $paymentInRecord->amount,
                    'payment_type' => $paymentInRecord->payment_type,
                    'reference_no' => $paymentInRecord->reference_no,
                    'receipt_no' => $paymentInRecord->receipt_no,
                    'description' => $paymentInRecord->description,
                    'items' => [
                        [
                            'name' => $paymentInRecord->payment_type . ' Payment',
                            'quantity' => 1,
                            'price' => $paymentInRecord->amount,
                            'amount' => $paymentInRecord->amount,
                        ]
                    ],
                ];
            }
        }

        // Get all payment invoices
        $allPaymentIns = PaymentIn::with(['party', 'bankAccount'])->latest()->get();

        return view('invoice.payment-in', compact('reactCss', 'reactJs', 'paymentIn', 'allPaymentIns'));
    }
}
