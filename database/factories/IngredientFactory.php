<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class IngredientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $stock = fake()->unique()->randomNumber();

        return [
            'name' => fake()->word(),
            'start_stock' => $stock,
            'stock' => $stock,
            'is_merchant_notified' => false,
        ];
    }
}
