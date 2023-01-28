<?php

namespace Tests\Feature\Api\V1;

use App\Enums\OrderMessageEnum;
use App\Events\IngredientsReachBelowPercentage;
use App\Mail\IngredientReachPercentageLimit;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Testing\TestResponse;
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
        $this->makeOrder()->assertUnauthorized();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_order_store_success_response()
    {
        $this->login();
        $this->makeOrder()
            ->assertJsonStructure([
                'data' => [
                    'message',
                ],
            ])
            ->assertJson([
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
        $this->makeOrder()->assertOk();
        $this->assertEquals(1, Order::count());
        $order = Order::first();
        $this->assertEquals($user->id, $order->user_id);

        $this->assertDatabaseHas('order_product', [
            'order_id' => $order->id,
            'product_id' => self::DEFAULT_PRODUCT_ID,
            'quantity' => $quantity,
        ]);

        $ingredients = Product::with('ingredients')->find(self::DEFAULT_PRODUCT_ID)->ingredients;
        $ingredients->each(fn ($ingredient) => $this->assertDatabaseHas('ingredients', [
            'name' => $ingredient->name,
            'id' => $ingredient->id,
            'start_stock' => $ingredient->start_stock,
            'stock' => $ingredient->start_stock - $ingredient->pivot->quantity * $quantity,
            'is_merchant_notified' => false,
        ]))->each(fn ($ingredient) => $this->assertDatabaseHas('ingredient_order_product', [
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
                ->map(fn ($ingredient) => $ingredient->getCurrentStockPercentage() < intval(config('main.limit_percentage_notification')))
                ->first(fn ($isBelowPercentage) => $isBelowPercentage)
        ) {
            $this->makeOrder()->assertOk();
        }

        Event::assertDispatched(IngredientsReachBelowPercentage::class);
    }

    public function test_when_ingredient_reach_below_limit_percentage_email_will_send_one_time()
    {
        Mail::fake();
        $this->login();
        $ingredients = Product::with('ingredients')->find(self::DEFAULT_PRODUCT_ID)->ingredients;
        // Here the ingredient will reach the limit of level to notify the merchant
        $this->assertDatabaseMissing('ingredients', [
            'is_merchant_notified' => true,
        ]);
        while (
            ! $ingredients
                ->fresh()
                ->map(fn ($ingredient) => $ingredient->getCurrentStockPercentage() < intval(config('main.limit_percentage_notification')))
                ->first(fn ($isBelowPercentage) => $isBelowPercentage)
        ) {
            $this->makeOrder()->assertOk();
        }

        // Try to another request to notify the merchant to confirm it will send only one time
        $this->makeOrder()->assertOk();
        $this->assertDatabaseHas('ingredients', [
            'is_merchant_notified' => true,
        ]);
        Mail::assertSent(IngredientReachPercentageLimit::class, 1);
    }

    public function test_when_ingredient_out_of_stock()
    {
        Log::shouldReceive('error')->once();
        Mail::fake();
        $this->login();
        $ingredients = Product::with('ingredients')->find(self::DEFAULT_PRODUCT_ID)->ingredients;
        $this->assertDatabaseMissing('ingredients', [
            'is_merchant_notified' => true,
        ]);
        while (
            ! $ingredients
                ->fresh()
                ->map(fn ($ingredient) => $ingredient->stock == 0)
                ->first(fn ($currenStockCanNotMakeProduct) => $currenStockCanNotMakeProduct)
        ) {
            $this->makeOrder();
        }
        // now the stock of one of ingredient is one so the order must fail
        $this->makeOrder()
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure(
                ['errors', 'message']
            )->assertJson(
                [
                    'errors' => [],
                    'message' => OrderMessageEnum::FAILED_MESSAGE->value,
                ]
            );

        $this->assertDatabaseHas('ingredients', [
            'is_merchant_notified' => true,
        ]);
        Mail::assertSent(IngredientReachPercentageLimit::class, 1);
    }

    public function test_store_order_validation_for_products()
    {
        $this->login();
        $this->postJson(route(self::ORDER_STORE_ROUTE_NAME))
            ->assertUnprocessable()
            ->assertJsonStructure(['errors', 'message'])
            ->assertJson(
                [
                    'errors' => [
                        'products' => [
                            'The products field is required.',
                        ],
                    ],
                    'message' => 'The products field is required.',
                ]
            );
    }

    public function test_store_order_validation_for_quantity()
    {
        $this->login();
        $this->postJson(route(self::ORDER_STORE_ROUTE_NAME, [
            'products' => [
                [
                    'product_id' => self::DEFAULT_PRODUCT_ID,
                ],
            ],
        ]))
            ->assertUnprocessable()
            ->assertJsonStructure(['errors', 'message'])
            ->assertJson(
                [
                    'errors' => [
                        'products.0.quantity' => [
                            'The quantity field is required.',
                        ],
                    ],
                    'message' => 'The quantity field is required.',
                ]
            );
    }

    public function test_store_order_validation_for_product_id()
    {
        $this->login();
        $this->postJson(route(self::ORDER_STORE_ROUTE_NAME, [
            'products' => [
                [
                    'product_id' => self::DEFAULT_PRODUCT_ID + 99999,
                    'quantity' => 1,
                ],
            ],
        ]))
            ->assertUnprocessable()
            ->assertJsonStructure(['errors', 'message'])
            ->assertJson(
                [
                    'errors' => [
                        'products.0.product_id' => [
                            'The selected product is invalid.',
                        ],
                    ],
                    'message' => 'The selected product is invalid.',
                ]
            );
    }

    /**
     * @param  int  $quantity
     * @return TestResponse
     */
    private function makeOrder(int $quantity = 2)
    {
        return $this->postJson(route(self::ORDER_STORE_ROUTE_NAME), [
            'products' => [
                [
                    'product_id' => self::DEFAULT_PRODUCT_ID,
                    'quantity' => $quantity,
                ],
            ],
        ]);
    }
}
