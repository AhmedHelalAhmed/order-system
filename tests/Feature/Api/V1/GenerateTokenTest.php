<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class GenerateTokenTest extends TestCase
{
    use LazilyRefreshDatabase;

    const TOKENS_STORE_ROUTE_NAME = 'api.v1.tokens.store';

    const DEFAULT_PRODUCT_ID = 1;

    public function test_store_tokens_success_response()
    {
        $user = User::factory()->create();
        $this->postJson(route(self::TOKENS_STORE_ROUTE_NAME), [
            'device_name' => 'test_api',
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'token',
                ],
            ]);
    }

    public function test_store_tokens_validation()
    {
        $this->postJson(route(self::TOKENS_STORE_ROUTE_NAME))
            ->assertUnprocessable()
            ->assertJsonStructure([
                'message',
                'errors',
            ])->assertJson(
                [
                    'message' => 'The email field is required. (and 2 more errors)',
                    'errors' => [
                        'email' => [
                            'The email field is required.',
                        ],
                        'password' => [
                            'The password field is required.',
                        ],
                        'device_name' => [
                            'The device name field is required.',
                        ],
                    ],
                ]
            );
    }

    public function test_store_tokens_validation_wrong_password()
    {
        $user = User::factory()->create();
        $this->postJson(route(self::TOKENS_STORE_ROUTE_NAME), [
            'device_name' => 'test_api',
            'email' => $user->email,
            'password' => 'test',
        ])->assertUnprocessable()
            ->assertJsonStructure([
                'message',
                'errors',
            ])->assertJson(
                [
                    'message' => 'The provided credentials are incorrect.',
                ]
            );
    }
}
