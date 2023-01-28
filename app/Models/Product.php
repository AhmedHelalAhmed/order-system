<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * @return BelongsToMany
     */
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }

    // TODO add unit test for this
    public function orders()
    {
        return $this->belongsToMany(Order::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }

    // TODO add unit test for this
    /**
     * @return HasMany
     */
    public function ingredientOrderProducts()
    {
        return $this->hasMany(IngredientOrderProduct::class);
    }

    /**
     * @param  int  $productId
     * @return Collection
     */
    public static function getIngredientsByProduct(int $productId): Collection
    {
        return self::with('ingredients')->find($productId, ['id'])->ingredients;
    }
}
