<?php

use App\Http\Controllers\Api\V1\StoreTokenController;
use App\Http\Controllers\Api\V1\OrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')
    ->group(function () {
        Route::post('/tokens', StoreTokenController::class);
        Route::post('/orders', OrderController::class)
            ->middleware('auth:sanctum')
            ->name('api.v1.orders.store');
    });

