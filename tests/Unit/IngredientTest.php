<?php

namespace Tests\Unit;

use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class IngredientTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_ingredients_database_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('ingredients', [
                'name',
                'start_stock',
                'stock',
                'is_merchant_notified',
            ]), __METHOD__);
    }

    public function test_ingredient_belongs_to_many()
    {
        $ingredient = Ingredient::factory()->create();
        $product = Product::factory()->create();

        $ingredient->products()->attach($product, ['quantity' => 50]);

        $this->assertInstanceOf(Collection::class, $ingredient->products);
        $this->assertInstanceOf(BelongsToMany::class, $ingredient->products());

        $this->assertEquals(1, $ingredient->products()->count());
        $this->assertEquals($product->id, $ingredient->products()->first()->id);
    }
}
