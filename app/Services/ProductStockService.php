<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Arr;

class ProductStockService
{
    private array $ingredientsNotificationToMerchant = [];

    public function execute(Order $order, array $products)
    {
        $orderProducts = [];
        foreach ($products as $product) {
            $productId = Arr::get($product, 'product_id');
            $quantity = Arr::get($product, 'quantity');
            $this->updateStock($order, $productId, $quantity);
            $orderProducts[$productId] = ['quantity' => $quantity];
        }

        return [
            'orderProducts' => $orderProducts,
            'ingredientsNotificationToMerchant' => $this->ingredientsNotificationToMerchant
        ];
    }

    /**
     * @param int $productId
     * @param int $quantity
     * @return void
     */
    private function updateStock(Order $order, int $productId, int $quantity)
    {
        $ingredients = Product::with('ingredients')->find($productId, ['id'])->ingredients;
        foreach ($ingredients as $ingredient) {
            $quantityToMakeTheProduct = $ingredient->pivot->quantity * $quantity;
            $currentStock = $ingredient->stock;
            throw_if($currentStock < $quantityToMakeTheProduct, new \Exception('ingredient out of stock'));
            $ingredient->decrement('stock', $quantityToMakeTheProduct);
            $percentageOfStockAfterThat = (($currentStock - $quantityToMakeTheProduct) / $ingredient->start_stock) * 100;
            if ($percentageOfStockAfterThat < 0.5 && !$ingredient->is_merchant_notified) {
                $this->ingredientsNotificationToMerchant[] = $ingredient->id;
            }
            //TODO add history for product ingredients order history
            //$order
        }
    }
}
