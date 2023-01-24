<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (! User::count()) {
            User::factory()->create([
                'name' => User::DEFAULT_CUSTOMER_NAME,
                'email' => User::DEFAULT_CUSTOMER_EMAIL,
            ]);
        }
    }
}
