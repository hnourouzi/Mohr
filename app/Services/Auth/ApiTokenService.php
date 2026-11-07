<?php

namespace App\Services\Auth;

use App\Models\User;
use Laravel\Sanctum\NewAccessToken;

class ApiTokenService
{
    public function createToken(User $user, ?string $deviceName = null): NewAccessToken
    {
        $name = $deviceName ?: 'api-token';

        return $user->createToken($name, ['*']);
    }
}
