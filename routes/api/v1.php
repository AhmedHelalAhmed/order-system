<?php

use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\StoreTokenController;
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
    ->name('api.v1.')
    ->group(function () {
        Route::post('/tokens', StoreTokenController::class)
            ->name('tokens.store');
        Route::post('/orders', OrderController::class)
            ->middleware('auth:sanctum')
            ->name('orders.store');
    });
