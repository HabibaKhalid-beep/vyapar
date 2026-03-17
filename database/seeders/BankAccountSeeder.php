<?php

namespace Database\Seeders;

use App\Models\BankAccount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankAccountSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $accounts = [
            [
                'display_name' => 'Main Road Canova Solution',
                'opening_balance' => 10000,
                'as_of_date' => now(),
                'account_number' => '1234567890',
                'swift_code' => 'XYZABC12',
                'iban' => 'IN00BANK000000000000',
                'bank_name' => 'First Bank',
                'account_holder_name' => 'Canva Solution',
                'print_on_invoice' => true,
            ],
        ];

        foreach ($accounts as $account) {
            BankAccount::updateOrCreate(['display_name' => $account['display_name']], $account);
        }
    }
}
