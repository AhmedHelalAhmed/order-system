<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TokenService
{
    /**
     * @param  string  $email
     * @param  string  $password
     * @param  string  $deviceName
     * @return string
     *
     * @throws ValidationException
     */
    public function getToken(string $email, string $password, string $deviceName): string
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken($deviceName)->plainTextToken;
    }
}
