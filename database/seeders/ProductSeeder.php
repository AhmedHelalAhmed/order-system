<?php

namespace Database\Seeders;

use App\Enums\IngredientEnum;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    const DEFAULT_INGREDIENTS_QUANTITIES_FOR_BURGER = [
        IngredientEnum::BEEF->value => 150,
        IngredientEnum::CHEESE->value => 30,
        IngredientEnum::ONION->value => 20,
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! Product::count()) {
            DB::beginTransaction();
            $product = Product::create([
                'name' => 'Burger',
            ]);
            $beef = Ingredient::where('name', IngredientEnum::BEEF->value)->first();
            $cheese = Ingredient::where('name', IngredientEnum::CHEESE->value)->first();
            $onion = Ingredient::where('name', IngredientEnum::ONION->value)->first();
            $product->ingredients()->attach([
                $beef->id => ['quantity' => self::DEFAULT_INGREDIENTS_QUANTITIES_FOR_BURGER[IngredientEnum::BEEF->value]],
                $cheese->id => ['quantity' => self::DEFAULT_INGREDIENTS_QUANTITIES_FOR_BURGER[IngredientEnum::CHEESE->value]],
                $onion->id => ['quantity' => self::DEFAULT_INGREDIENTS_QUANTITIES_FOR_BURGER[IngredientEnum::ONION->value]],
            ]);
            DB::commit();
        }
    }
}
