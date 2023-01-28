<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class IngredientOrderProduct extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'ingredient_id',
        'product_id',
        'quantity',
        'total_quantity',
    ];

    // TODO add unit test for this
    /**
     * @return BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // TODO add unit test for this
    /**+
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // TODO add unit test for this
    /**
     * @return BelongsTo
     */
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
