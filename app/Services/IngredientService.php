<?php

namespace App\Services;

use App\Models\IngredientOrderProduct;
use App\Models\Order;
use App\Models\Product;
use Exception;

class IngredientService
{
    /**
     * @param  Order  $order
     * @param  int  $productId
     * @param  int  $quantity
     * @return array
     *
     * @throws \Throwable
     */
    public function updateStock(int $orderId, int $productId, int $quantity): array
    {
        $ingredientsNotificationToMerchant = [];
        $ingredients = Product::with('ingredients')->find($productId, ['id'])->ingredients;
        foreach ($ingredients as $ingredient) {
            $quantityToMakeTheProduct = $ingredient->pivot->quantity * $quantity;
            $currentStock = $ingredient->stock;
            throw_if($currentStock < $quantityToMakeTheProduct, new Exception('ingredient out of stock'));
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
