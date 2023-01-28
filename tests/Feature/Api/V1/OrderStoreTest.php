<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderStoreTest extends TestCase
{
    use LazilyRefreshDatabase;

    const ORDER_STORE_ROUTE_NAME = 'api.v1.orders.store';
    const DEFAULT_PRODUCT_ID = 1;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_order_store_success_response()
    {

        Sanctum::actingAs(
            User::factory()->create()
        );
        $response = $this->postJson(route(self::ORDER_STORE_ROUTE_NAME), [
            "products" => [
                [
                    "product_id" => self::DEFAULT_PRODUCT_ID,
                    "quantity" => 2
                ]
            ]
        ]);
        $response->assertOk();
    }
}
