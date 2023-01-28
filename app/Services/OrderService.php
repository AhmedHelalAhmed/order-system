<?php

namespace App\Services;

use App\Events\IngredientsReachBelowPercentage;
use App\Models\Order;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * @var ProductService
     */
    private ProductService $productService;

    /**
     * @param ProductService $productStockService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @param array $data
     * @param int $userId
     * @return bool
     * @throws \Throwable
     */
    public function execute(array $data, int $userId): bool
    {
        try {
            DB::beginTransaction();
            $ingredientsNotificationToMerchant = $this->productService->processOrderProdcuts(
                Order::create(['user_id' => $userId]),
                Arr::get($data, 'products')
            );
            DB::commit();
            if (count($ingredientsNotificationToMerchant)) {
                event(new IngredientsReachBelowPercentage(
                    $ingredientsNotificationToMerchant
                ));
            }
            return true;

        } catch (Exception $exception) {
            DB::rollBack();

            Log::error('[order-store]: error in order: ' . $exception->getMessage(), [
                'exception' => $exception
            ]);
            return false;
        }
    }
}