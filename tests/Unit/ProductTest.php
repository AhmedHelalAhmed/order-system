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

class ProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_products_database_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('products', [
                'name',
            ]), __METHOD__);
    }

    public function test_product_belongs_to_many()
    {
        $product = Product::factory()->create();
        $ingredientsCount = 5;
        $ingredients = Ingredient::factory($ingredientsCount)->create();
        $product->ingredients()
            ->attach(
                $ingredients->mapWithKeys(
                    fn ($ingredient, $key) => [$ingredient['id'] => ['quantity' => $this->faker->randomNumber()]]
                )->all()
            );

        $this->assertInstanceOf(Collection::class, $product->ingredients);
        $this->assertInstanceOf(BelongsToMany::class, $product->ingredients());
        $this->assertEquals($ingredientsCount, $product->ingredients()->count());
    }
}
