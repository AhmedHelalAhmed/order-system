<?php

namespace App\Services;

use App\Events\IngredientsReachBelowPercentage;
use App\Exceptions\IngredientOutOfStockException;
use App\Models\Order;
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
     * @param  ProductService  $productService
     */
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @param  array  $data
     * @param  int  $userId
     * @return bool
     *
     * @throws \Throwable
     */
    public function storeOrder(array $data, int $userId): bool
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
        } catch (IngredientOutOfStockException $exception) {
            DB::rollBack();

            Log::error('[order-store]: error in order: '.$exception->getMessage(), [
                'exception' => $exception,
            ]);

            return false;
        }
    }
}
