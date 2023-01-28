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
                'status',
                'data' => [
                    'token',
                ],
            ]);
    }
}
