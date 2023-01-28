<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\OrderMessageEnum;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StoreOrderRequest;
use App\Services\OrderService;

class OrderController extends ApiController
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function __invoke(StoreOrderRequest $request)
    {
        $status = $this->orderService->execute(
            $request->validated(),
            auth()->id()
        );

        if (! $status) {
            return $this->errorResponse(OrderMessageEnum::FAILED_MESSAGE->value);
        }

        return $this->sucessResponse(['message' => OrderMessageEnum::SUCCESS_MESSAGE->value]);
    }
}
