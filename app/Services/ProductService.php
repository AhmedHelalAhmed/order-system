<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Arr;

class ProductService
{
    /**
     * @var IngredientService
     */
    private IngredientService $ingredientService;

    /**
     * @param IngredientService $ingredientService
     */
    public function __construct(IngredientService $ingredientService)
    {
        $this->ingredientService = $ingredientService;
    }

    /**
     * @param Order $order
     * @param array $products
     * @return array
     * @throws \Throwable
     */
    public function processOrderProdcuts(Order $order, array $products)
    {
        $ingredientsNotificationToMerchant = [];
        $orderProducts = [];
        foreach ($products as $product) {
            $productId = Arr::get($product, 'product_id');
            $quantity = Arr::get($product, 'quantity');
            $ingredientsNotificationToMerchant = array_merge(
                $ingredientsNotificationToMerchant,
                $this->ingredientService->updateStock($order->id, $productId, $quantity)
            );
            $orderProducts[$productId] = ['quantity' => $quantity];
        }
        $order->products()->attach($orderProducts);
        return $ingredientsNotificationToMerchant;
    }
}
