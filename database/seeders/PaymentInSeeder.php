<?php

namespace Database\Seeders;

use App\Models\PaymentIn;
use App\Models\Party;
use App\Models\BankAccount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentInSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Get sample parties and bank accounts
        $parties = Party::all();
        $bankAccounts = BankAccount::all();

        if ($parties->isEmpty() || $bankAccounts->isEmpty()) {
            $this->command->info('Skipping PaymentInSeeder: No parties or bank accounts found.');
            return;
        }

        // Create sample payment in records
        $paymentTypes = ['cash', 'cheque', 'bank_transfer', 'upi'];

        for ($i = 1; $i <= 5; $i++) {
            PaymentIn::create([
                'party_id' => $parties->random()->id,
                'bank_account_id' => $bankAccounts->random()->id,
                'amount' => rand(1000, 50000),
                'payment_type' => $paymentTypes[array_rand($paymentTypes)],
                'reference_no' => 'REF-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'receipt_no' => 'RCP-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'date' => now()->subDays(rand(0, 30)),
                'description' => 'Sample payment in record ' . $i,
            ]);
        }

        $this->command->info('PaymentIn sample records created successfully!');
    }
}
