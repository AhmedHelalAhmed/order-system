<?php

use App\Models\Ingredient;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // The usage of this table will appear if in the future
        // We want to know how many quantity required to make a specific order
        // Imagine the product ingredient changed or the quantity of in ingredient changed
        Schema::create('ingredient_order_product', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class)->constrained();
            $table->foreignIdFor(Product::class)->constrained();
            $table->foreignIdFor(Ingredient::class)->constrained();
            $table->unsignedInteger('quantity')
                ->comment('The quantity in grams of the ingredient to make the product in the order');
            $table->unsignedInteger('total_quantity')
                ->comment('The total quantity in grams of the ingredient to make the whole product quantity in the order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ingredient_order_product');
    }
};
