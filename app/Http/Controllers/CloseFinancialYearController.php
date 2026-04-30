<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\PaymentIn;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Transaction;
use App\Support\TransactionNumberPrefix;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CloseFinancialYearController extends Controller
{
    public function index()
    {
        $prefixes = TransactionNumberPrefix::all();
        $labels = TransactionNumberPrefix::labels();
        $previews = [
            'invoice' => TransactionNumberPrefix::format('invoice', (Sale::max('id') ?? 0) + 1),
            'estimate' => TransactionNumberPrefix::format('estimate', (Sale::max('id') ?? 0) + 1),
            'proforma_invoice' => TransactionNumberPrefix::format('proforma_invoice', (Sale::max('id') ?? 0) + 1),
            'payment_in' => TransactionNumberPrefix::format('payment_in', (PaymentIn::max('id') ?? 0) + 1),
            'delivery_challan' => TransactionNumberPrefix::format('delivery_challan', (Sale::max('id') ?? 0) + 1),
            'sale_order' => TransactionNumberPrefix::format('sale_order', (Sale::max('id') ?? 0) + 1),
            'purchase_order' => TransactionNumberPrefix::format('purchase_order', (Purchase::where('type', 'purchase_order')->max('id') ?? 0) + 1),
            'credit_note' => TransactionNumberPrefix::format('credit_note', (Sale::where('type', 'sale_return')->max('id') ?? 0) + 1),
            'purchase_bill' => TransactionNumberPrefix::format('purchase_bill', (Purchase::where('type', 'purchase')->max('id') ?? 0) + 1),
            'purchase_return' => TransactionNumberPrefix::format('purchase_return', (Purchase::where('type', 'purchase_return')->max('id') ?? 0) + 1),
        ];

        return view('dashboard.utilities.close-financial-year', [
            'prefixes' => $prefixes,
            'labels' => $labels,
            'previews' => $previews,
            'lastBackupDate' => AppSetting::getValue('close_financial_year_last_date'),
            'lastBackupFile' => AppSetting::getValue('close_financial_year_last_file'),
        ]);
    }

    public function updatePrefixes(Request $request)
    {
        $rules = [];
        foreach (array_keys(TransactionNumberPrefix::defaults()) as $type) {
            $rules['prefixes.' . $type] = ['nullable', 'regex:/^$|^[A-Za-z0-9][A-Za-z0-9-]*$/'];
        }

        $data = $request->validate($rules, [
            'regex' => 'Prefix empty ho sakta hai. Letters, numbers aur hyphen allowed hain.',
        ]);

        foreach (($data['prefixes'] ?? []) as $type => $prefix) {
            AppSetting::setValue(TransactionNumberPrefix::settingKey($type), strtoupper((string) $prefix));
        }

        return redirect()
            ->route('utilities.close-financial-year')
            ->with('success', 'Transaction prefixes updated successfully.');
    }

    public function backupAndStartFresh(Request $request)
    {
        $data = $request->validate([
            'closing_date' => ['required', 'date'],
        ]);

        $closingDate = $data['closing_date'];
        $payload = [
            'closing_date' => $closingDate,
            'sales' => Sale::whereDate('created_at', '<=', $closingDate)->get()->toArray(),
            'purchases' => Purchase::whereDate('created_at', '<=', $closingDate)->get()->toArray(),
            'transactions' => Transaction::whereDate('created_at', '<=', $closingDate)->get()->toArray(),
        ];

        $directory = 'financial-year-backups';
        $fileName = 'close-year-' . now()->format('YmdHis') . '.json';
        Storage::disk('local')->put($directory . '/' . $fileName, json_encode($payload, JSON_PRETTY_PRINT));

        AppSetting::setValue('close_financial_year_last_date', $closingDate);
        AppSetting::setValue('close_financial_year_last_file', $directory . '/' . $fileName);

        return redirect()
            ->route('utilities.close-financial-year')
            ->with('success', 'Backup created successfully. Closing date saved as ' . $closingDate . '.');
    }
}
