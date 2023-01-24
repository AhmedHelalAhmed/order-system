<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_users_database_columns()
    {
        $this->assertTrue(
            Schema::hasColumns('users', [
                'name',
                'email',
                'password',
            ]), __METHOD__);
    }

    public function test_user_has_many_orders()
    {
        $user = User::factory()->create();
        Order::factory()->for($user)->create();

        $this->assertInstanceOf(Collection::class, $user->orders);
        $this->assertInstanceOf(HasMany::class, $user->orders());
        $this->assertEquals(1, $user->orders()->count());
    }
}
