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
