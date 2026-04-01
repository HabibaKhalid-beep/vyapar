<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Potato', 'unit' => 'KG', 'sale_price' => 200],
            ['name' => 'Tomato', 'unit' => 'KG', 'sale_price' => 120],
            ['name' => 'Onion', 'unit' => 'KG', 'sale_price' => 80],
            ['name' => 'Egg', 'unit' => 'KG', 'sale_price' => 1200],
            ['name' => 'Apple', 'unit' => 'KG', 'sale_price' => 150],
            ['name' => 'Banana', 'unit' => 'KG', 'sale_price' => 60],
            ['name' => 'Grapes', 'unit' => 'KG', 'sale_price' => 180],
            ['name' => 'Mango', 'unit' => 'KG', 'sale_price' => 250],
        ];

        foreach ($items as $item) {
            Item::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
