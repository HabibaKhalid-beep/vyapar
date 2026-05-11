<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    //

    public function general()
    {
        return view('dashboard.settings.general', [
            'bankAccountPasswordSet' => filled(AppSetting::getValue('bank_account_password')),
        ]);
    }

    public function updateGeneral(Request $request)
    {
        $data = $request->validate([
            'bank_account_password' => ['nullable', 'string', 'min:4', 'max:255'],
        ]);

        if (!empty($data['bank_account_password'])) {
            AppSetting::setValue('bank_account_password', Hash::make($data['bank_account_password']));
        }

        return redirect()
            ->route('settings.general')
            ->with('success', 'General settings updated successfully.');
    }

    public function transactions()
    {
        return view('dashboard.settings.transactions', [
            'countEnabled' => (bool) AppSetting::getValue('transaction_items_count_enabled', false),
            'customerPoDetailsEnabled' => (bool) AppSetting::getValue('transaction_customer_po_enabled', false),
        ]);
    }

    public function updateTransactions(Request $request)
    {
        $data = $request->validate([
            'count_enabled' => ['nullable', 'boolean'],
            'customer_po_enabled' => ['nullable', 'boolean'],
        ]);

        if ($request->has('count_enabled')) {
            AppSetting::setValue('transaction_items_count_enabled', !empty($data['count_enabled']) ? '1' : '0');
        }
        if ($request->has('customer_po_enabled')) {
            AppSetting::setValue('transaction_customer_po_enabled', !empty($data['customer_po_enabled']) ? '1' : '0');
        }

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Transaction settings updated successfully.',
                'count_enabled' => !empty($data['count_enabled']),
                'customer_po_enabled' => !empty($data['customer_po_enabled']),
            ]);
        }

        return redirect()
            ->route('settings.transactions')
            ->with('success', 'Transaction settings updated successfully.');
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
