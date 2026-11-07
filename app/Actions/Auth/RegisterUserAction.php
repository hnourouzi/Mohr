<?php

namespace App\Actions\Auth;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterUserAction
{
    public function execute(string $name, string $email, string $password, bool $dispatchRegisteredEvent = true): User
    {
        $user = DB::transaction(function () use ($name, $email, $password) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => UserRole::User,
            ]);

            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
            ]);

            return $user->fresh(['wallet']);
        });

        if ($dispatchRegisteredEvent) {
            event(new Registered($user));
        }

        return $user;
    }
}
