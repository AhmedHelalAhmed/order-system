<?php

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
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('start_stock')
                ->comment('The stock in grams that we start with and required to calculate stock level percentage');
            $table->unsignedInteger('stock')->comment('The current stock in grams');
            $table->boolean('is_merchant_notified')
                ->default(false)
                ->comment('If true then merchant is notified when the ingredients reach below 50% for first time hence we don\'t send the email again');
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
        Schema::dropIfExists('ingredients');
    }
};
