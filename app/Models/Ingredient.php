<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_stock',
        'stock',
        'is_merchant_notified',
    ];

    /**
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
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
     * @param int $quantityToDecrease
     * @return bool
     */
    public function isCurrentStockReadyForMerchantNotification(int $quantityToDecrease): bool
    {
        return ($this->stock - $quantityToDecrease / $this->start_stock) * 100 < intval(config('main.limit_percentage_notification'));
    }

    /**
     * @return bool
     */
    public function isMerchantNotNotified(): bool
    {
        return !$this->is_merchant_notified;
    }

    /**
     * @param int $quantityToDecrease
     * @return bool
     */
    public function isMerchantStockNotificationReady(int $quantityToDecrease): bool
    {
        return $this->isCurrentStockReadyForMerchantNotification($quantityToDecrease) && $this->isMerchantNotNotified();
    }
}
