<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderMessageEnum;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StoreOrderRequest;
use App\Services\OrderService;

class OrderController extends ApiController
{
    private OrderService $orderService;

    /**
     * @param  OrderService  $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @param  StoreOrderRequest  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function __invoke(StoreOrderRequest $request)
    {
        $status = $this->orderService->storeOrder(
            $request->validated(),
            auth()->id()
        );

        if (! $status) {
            return $this->errorResponse(OrderMessageEnum::FAILED_MESSAGE->value);
        }

        return $this->sucessResponse(['message' => OrderMessageEnum::SUCCESS_MESSAGE->value]);
    }
}
