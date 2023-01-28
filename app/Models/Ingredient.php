<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
     * @param  int  $quantityToDecrease
     * @return bool
     */
    public function isCurrentStockReadyForMerchantNotification(int $quantityToDecrease): bool
    {
        return $this->getCurrentStockPercentage($quantityToDecrease) < intval(config('main.limit_percentage_notification'));
    }

    /**
     * @return bool
     */
    public function isMerchantNotNotified(): bool
    {
        return ! $this->is_merchant_notified;
    }

    /**
     * @param  int  $quantityToDecrease
     * @return bool
     */
    public function isMerchantStockNotificationReady(int $quantityToDecrease): bool
    {
        return $this->isCurrentStockReadyForMerchantNotification($quantityToDecrease) && $this->isMerchantNotNotified();
    }

    /**
     * @param  int  $quantityToDecrease
     * @return float|int
     */
    public function getCurrentStockPercentage(int $quantityToDecrease = 0)
    {
        return (($this->stock - $quantityToDecrease) / $this->start_stock) * 100;
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeMerchantNotNotified(Builder $query)
    {
        return $query->where('is_merchant_notified', false);
    }

    /**
     * @param  array  $ingredientsIds
     * @return mixed
     */
    public static function getWithMerchantNotNotified(array $ingredientsIds)
    {
        return self::select('name', 'id')
            ->whereIn('id', $ingredientsIds)
            ->merchantNotNotified()
            ->get();
    }

    /**
     * @param  array  $ingredientsIds
     * @return void
     */
    public static function updateMerchantToNotified(array $ingredientsIds)
    {
        self::whereIn('id', $ingredientsIds)->update(['is_merchant_notified' => true]);
    }
}
