<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\Api\V1\StoreTokenRequest;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class StoreTokenController extends ApiController
{
    private TokenService $service;

    /**
     * @param  TokenService  $service
     */
    public function __construct(TokenService $service)
    {
        $this->service = $service;
    }

    /**
     * @param  StoreTokenRequest  $request
     * @return JsonResponse
     *
     * @throws ValidationException
     */
    public function __invoke(StoreTokenRequest $request)
    {
        return $this->sucessResponse([
            'token' => $this->service->getToken(
                $request->get('email'),
                $request->get('password'),
                $request->get('device_name')
            ),
        ]);
    }
}
