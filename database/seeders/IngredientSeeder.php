<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use Illuminate\Database\Seeder;

class IngredientSeeder extends Seeder
{
    const DEFAULT_INGREDIENTS = [
        [
            'name' => 'Beef',
            'start_stock' => 20000,
            'stock' => 20000,
        ],
        [
            'name' => 'Cheese',
            'start_stock' => 5000,
            'stock' => 5000,
        ],
        [
            'name' => 'Onion',
            'start_stock' => 1000,
            'stock' => 1000,
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! Ingredient::count()) {
            $timeDateNow = now();
            Ingredient::insert(collect(self::DEFAULT_INGREDIENTS)
                ->transform(fn ($ingredient) => array_merge($ingredient, [
                    'created_at' => $timeDateNow,
                    'updated_at' => $timeDateNow,
                ]))->toArray());
        }
    }
}
