<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Arr;

class OrderService
{
    private ProductStockService $productStockService;

    public function __construct(ProductStockService $productStockService)
    {
        $this->productStockService = $productStockService;
    }

    /**
     * @param array $data
     * @param int $userId
     * @return array
     */
    public function execute(array $data, int $userId):array
    {
        $order = Order::create(['user_id' => $userId]);
        [
            'orderProducts' => $orderProducts,
            'ingredientsNotificationToMerchant' => $ingredientsNotificationToMerchant
        ] = $this->productStockService->execute($order,Arr::get($data, 'products'));
        $order->products()->attach($orderProducts);

        return $ingredientsNotificationToMerchant;
    }
}
