<?php

namespace Database\Seeders;

use App\Models\Item;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $items = [
            ['name' => 'Potato', 'unit' => 'KG', 'price' => 200, 'description' => 'Fresh potatoes'],
            ['name' => 'Tomato', 'unit' => 'KG', 'price' => 120, 'description' => 'Fresh tomatoes'],
            ['name' => 'Onion', 'unit' => 'KG', 'price' => 80, 'description' => 'Fresh onions'],
             ['name' => 'Egg', 'unit' => 'KG', 'price' => 1200, 'description' => 'Fresh eggs'],
             ['name' => 'Apple', 'unit' => 'KG', 'price' => 150, 'description' => 'Fresh apples'],
             ['name' => 'Banana', 'unit' => 'KG', 'price' => 60, 'description' => 'Fresh bananas'],
             ['name' => 'Grapes', 'unit' => 'KG', 'price' => 180, 'description' => 'Fresh grapes'],
             ['name' => 'Mango', 'unit' => 'KG', 'price' => 250, 'description' => 'Fresh mangoes'],

        ];

        foreach ($items as $item) {
            Item::updateOrCreate(['name' => $item['name']], $item);
        }
    }
}
