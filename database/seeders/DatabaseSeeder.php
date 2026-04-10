<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin1@gmail.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
        ]);

        $this->call([
            PartySeeder::class,
            BankAccountSeeder::class,
            ItemSeeder::class,
            AddMissingPermissionsSeeder::class,
            PaymentInSeeder::class,
        ]);
    }
}
