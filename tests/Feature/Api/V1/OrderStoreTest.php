<?php

namespace Tests\Feature\Api\V1;

use App\Enums\OrderMessageEnum;
use App\Events\IngredientsReachBelowPercentage;
use App\Mail\IngredientReachPercentageLimit;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
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
     * @return User
     */
    private function login(): User
    {
        $user = User::factory()->create();

        Sanctum::actingAs(
            $user
        );

        return $user;
    }

    public function test_unauthenticated_user_can_not_make_order()
    {
        $response = $this->postJson(route(self::ORDER_STORE_ROUTE_NAME), [
            'products' => [
                [
                    'product_id' => self::DEFAULT_PRODUCT_ID,
                    'quantity' => 2,
                ],
            ],
        ]);
        $response->assertUnauthorized();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_order_store_success_response()
    {
        $this->login();
        $this->postJson(route(self::ORDER_STORE_ROUTE_NAME), [
            'products' => [
                [
                    'product_id' => self::DEFAULT_PRODUCT_ID,
                    'quantity' => 2,
                ],
            ],
        ])
            ->assertJsonStructure([
                'status',
                'data' => [
                    'message',
                ],
            ])
            ->assertJson([
                'status' => true,
                'data' => [
                    'message' => OrderMessageEnum::SUCCESS_MESSAGE->value,
                ],
            ])
            ->assertOk();
    }

    public function test_order_stored_in_database_successfully()
    {
        $user = $this->login();
        $this->assertEquals(0, Order::count());
        $quantity = 2;
        $this->postJson(route(self::ORDER_STORE_ROUTE_NAME), [
            'products' => [
                [
                    'product_id' => self::DEFAULT_PRODUCT_ID,
                    'quantity' => $quantity,
                ],
            ],
        ])->assertOk();
        $this->assertEquals(1, Order::count());
        $order = Order::first();
        $this->assertEquals($user->id, $order->user_id);

        $this->assertDatabaseHas('order_product', [
            'order_id' => $order->id,
            'product_id' => self::DEFAULT_PRODUCT_ID,
            'quantity' => $quantity,
        ]);

        $ingredients = Product::with('ingredients')->find(self::DEFAULT_PRODUCT_ID)->ingredients;
        $ingredients->each(fn ($ingredient) => $this->assertDatabaseHas('ingredient_order_product', [
            'order_id' => $order->id,
            'product_id' => $ingredient->pivot->product_id,
            'ingredient_id' => $ingredient->id,
            'quantity' => $ingredient->pivot->quantity,
            'total_quantity' => $ingredient->pivot->quantity * $quantity,
        ]));
    }

    public function test_when_ingredient_reach_below_limit_percentage_event_of_ingredients_reach_below_percentage_fired()
    {
        Event::fake();
        $this->login();
        $ingredients = Product::with('ingredients')->find(self::DEFAULT_PRODUCT_ID)->ingredients;
        while (
            ! $ingredients
                ->fresh()
                ->map(fn ($ingredient) => $ingredient->getCurrentStockPercentage() < 50)
                ->first(fn ($isBelowPercentage) => $isBelowPercentage)
        ) {
            $this->postJson(route(self::ORDER_STORE_ROUTE_NAME), [
                'products' => [
                    [
                        'product_id' => self::DEFAULT_PRODUCT_ID,
                        'quantity' => 2,
                    ],
                ],
            ])->assertOk();
        }

        Event::assertDispatched(IngredientsReachBelowPercentage::class);
    }

    public function test_when_ingredient_reach_below_limit_percentage_email_will_send_one_time()
    {
        Mail::fake();
        $this->login();
        $ingredients = Product::with('ingredients')->find(self::DEFAULT_PRODUCT_ID)->ingredients;
        // Here the ingredient will reach the limit of level to notify the merchant
        while (
            ! $ingredients
                ->fresh()
                ->map(fn ($ingredient) => $ingredient->getCurrentStockPercentage() < 50)
                ->first(fn ($isBelowPercentage) => $isBelowPercentage)
        ) {
            $this->postJson(route(self::ORDER_STORE_ROUTE_NAME), [
                'products' => [
                    [
                        'product_id' => self::DEFAULT_PRODUCT_ID,
                        'quantity' => 2,
                    ],
                ],
            ])->assertOk();
        }

        // Try to another request to notify the merchant to confirm it will send only one time
        $this->postJson(route(self::ORDER_STORE_ROUTE_NAME), [
            'products' => [
                [
                    'product_id' => self::DEFAULT_PRODUCT_ID,
                    'quantity' => 2,
                ],
            ],
        ])->assertOk();
        Mail::assertSent(IngredientReachPercentageLimit::class, 1);
    }
}
