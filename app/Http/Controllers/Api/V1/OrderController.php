<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\IngredientsReachBelowHalfPercentage;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StoreOrderRequest;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends ApiController
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function __invoke(StoreOrderRequest $request)
    {
        try {
            DB::beginTransaction();
            $ingredientsNotificationToMerchant = $this->orderService->execute(
                $request->validated(),
                auth()->id()
            );
            dd('here we go');
            DB::commit();
            if (count($ingredientsNotificationToMerchant)) {
                event(new IngredientsReachBelowHalfPercentage(
                    $ingredientsNotificationToMerchant
                ));
            }
        } catch (\Exception $exception) {
            DB::rollBack();

            Log::error('[order-store]: error in order: ' . $exception->getMessage(), [
                'exception' => $exception
            ]);
        }

    }
}
