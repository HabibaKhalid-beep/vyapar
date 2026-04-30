<?php

namespace App\Http\Controllers;

use App\Models\Broker;
use App\Models\Invoice;
use App\Models\InvoiceExpense;
use App\Models\Party;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimpleInvoiceController extends Controller
{
    public function create()
    {
        return view('dashboard.invoices.create', [
            'parties' => Party::orderBy('name')->get(),
            'brokers' => Broker::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'party_id' => ['required', 'exists:parties,id'],
            'broker_id' => ['nullable', 'exists:brokers,id'],
            'tadad' => ['required', 'integer', 'min:0'],
            'total_wazan' => ['nullable', 'numeric', 'min:0'],
            'safi_wazan' => ['required', 'numeric', 'min:0'],
            'rate' => ['required', 'numeric', 'min:0'],
            'deo' => ['nullable', 'string', 'max:255'],
            'broker_commission_type' => ['nullable', 'in:fixed,percent'],
            'broker_commission_value' => ['nullable', 'numeric', 'min:0'],
            'broker_commission' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'bardana' => ['nullable', 'numeric', 'min:0'],
            'mazdori' => ['nullable', 'numeric', 'min:0'],
            'rehra_mazdori' => ['nullable', 'numeric', 'min:0'],
            'dak_karaya' => ['nullable', 'numeric', 'min:0'],
            'brokeri' => ['nullable', 'numeric', 'min:0'],
            'local_izafi' => ['nullable', 'numeric', 'min:0'],
        ]);

        $safiWazan = round((float) ($data['safi_wazan'] ?? 0), 2);
        $rate = round((float) ($data['rate'] ?? 0), 2);
        $amount = round($safiWazan * $rate, 2);

        $brokerType = $data['broker_commission_type'] ?? null;
        $brokerValue = round((float) ($data['broker_commission_value'] ?? 0), 2);
        $brokerCommission = round((float) ($data['broker_commission'] ?? 0), 2);

        if ($brokerCommission <= 0 && $brokerType && $brokerValue > 0) {
            $brokerCommission = $brokerType === 'percent'
                ? round($amount * ($brokerValue / 100), 2)
                : round($safiWazan * $brokerValue, 2);
        }

        $expenseFields = ['bardana', 'mazdori', 'rehra_mazdori', 'dak_karaya', 'brokeri', 'local_izafi'];
        $expenses = [];
        foreach ($expenseFields as $field) {
            $expenses[$field] = round((float) ($data[$field] ?? 0), 2);
        }

        if (($expenses['brokeri'] ?? 0) <= 0 && $brokerCommission > 0) {
            $expenses['brokeri'] = $brokerCommission;
        }

        $totalExpense = round(array_sum($expenses), 2);
        $finalAmount = round($amount - $totalExpense, 2);

        $invoice = DB::transaction(function () use ($data, $amount, $brokerType, $brokerValue, $brokerCommission, $finalAmount, $expenses, $totalExpense) {
            $invoice = Invoice::create([
                'party_id' => $data['party_id'],
                'broker_id' => $data['broker_id'] ?? null,
                'tadad' => (int) $data['tadad'],
                'total_wazan' => round((float) ($data['total_wazan'] ?? 0), 2),
                'safi_wazan' => round((float) ($data['safi_wazan'] ?? 0), 2),
                'rate' => round((float) ($data['rate'] ?? 0), 2),
                'amount' => $amount,
                'deo' => $data['deo'] ?? null,
                'broker_commission_type' => $brokerType,
                'broker_commission_value' => $brokerValue,
                'broker_commission' => $brokerCommission,
                'final_amount' => $finalAmount,
                'notes' => $data['notes'] ?? null,
            ]);

            InvoiceExpense::create(array_merge($expenses, [
                'invoice_id' => $invoice->id,
                'total_expense' => $totalExpense,
            ]));

            return $invoice;
        });

        return redirect()
            ->route('market-invoices.show', $invoice)
            ->with('success', 'Invoice saved successfully.');
    }

    public function show(Invoice $marketInvoice)
    {
        return view('dashboard.invoices.show', [
            'invoice' => $marketInvoice->load(['party', 'broker', 'expenses']),
        ]);
    }
}
