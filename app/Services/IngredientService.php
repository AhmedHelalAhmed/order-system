<?php

namespace App\Services;

use App\Exceptions\IngredientOutOfStockException;
use App\Models\IngredientOrderProduct;
use App\Models\Product;

class IngredientService
{
    /**
     * @param  int  $orderId
     * @param  int  $productId
     * @param  int  $quantity
     * @return array
     *
     * @throws \Throwable
     */
    public function updateStock(int $orderId, int $productId, int $quantity): array
    {
        $ingredientsNotificationToMerchant = [];
        $ingredients = Product::getIngredientsByProduct($productId);
        foreach ($ingredients as $ingredient) {
            $quantityToMakeTheProduct = $ingredient->pivot->quantity * $quantity;
            $currentStock = $ingredient->stock;
            throw_if($currentStock < $quantityToMakeTheProduct, new IngredientOutOfStockException(
                "ingredient {$ingredient->name} with id {$ingredient->id} out of stock")
            );
            $ingredient->decrement('stock', $quantityToMakeTheProduct);
            if ($ingredient->isMerchantStockNotificationReady($quantityToMakeTheProduct)) {
                $ingredientsNotificationToMerchant[] = $ingredient->id;
            }
            IngredientOrderProduct::create([
                'order_id' => $orderId,
                'ingredient_id' => $ingredient->id,
                'product_id' => $productId,
                'quantity' => $ingredient->pivot->quantity,
                'total_quantity' => $quantityToMakeTheProduct,
            ]);
        }

        return $ingredientsNotificationToMerchant;
    }
}
