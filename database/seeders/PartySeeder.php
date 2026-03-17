<?php

namespace Database\Seeders;

use App\Models\Party;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PartySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $parties = [
            [
                'name' => 'Default Customer',
                'phone' => '0000000000',
                'email' => 'default@example.com',
                'billing_address' => '123 Default Street, City, Country',
                'shipping_address' => '123 Default Street, City, Country',
                'opening_balance' => 0,
                'credit_limit_enabled' => false,
                'custom_fields' => [],
            ],
            [
                'name' => 'Cash Sale',
                'phone' => '',
                'email' => '',
                'billing_address' => '',
                'shipping_address' => '',
                'opening_balance' => 0,
                'credit_limit_enabled' => false,
                'custom_fields' => [],
            ],
        ];

        foreach ($parties as $party) {
            Party::updateOrCreate(['name' => $party['name']], $party);
        }
    }
}
