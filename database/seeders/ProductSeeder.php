<?php

namespace Database\Seeders;

use App\Enums\DefaultProductEnum;
use App\Enums\IngredientEnum;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!Product::count()) {
            DB::beginTransaction();
            $product = Product::create([
                'name' => DefaultProductEnum::PRODUCT_NAME,
            ]);
            $beef = Ingredient::where('name', IngredientEnum::BEEF->value)->first();
            $cheese = Ingredient::where('name', IngredientEnum::CHEESE->value)->first();
            $onion = Ingredient::where('name', IngredientEnum::ONION->value)->first();
            $product->ingredients()->attach([
                $beef->id => ['quantity' => DefaultProductEnum::DEFAULT_INGREDIENTS_QUANTITIES_FOR_BURGER[IngredientEnum::BEEF->value]],
                $cheese->id => ['quantity' => DefaultProductEnum::DEFAULT_INGREDIENTS_QUANTITIES_FOR_BURGER[IngredientEnum::CHEESE->value]],
                $onion->id => ['quantity' => DefaultProductEnum::DEFAULT_INGREDIENTS_QUANTITIES_FOR_BURGER[IngredientEnum::ONION->value]],
            ]);
            DB::commit();
        }
    }
}
