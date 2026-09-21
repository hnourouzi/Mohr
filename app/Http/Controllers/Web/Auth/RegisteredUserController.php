<?php

namespace App\Http\Controllers\Web\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'password' => Hash::make($request->validated('password')),
                'role' => UserRole::User,
            ]);

            // هر کاربر جدید، همزمان یه کیف پول خالی می‌گیره
            Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
            ]);

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('panel.dashboard')
            ->with('success', 'حساب شما با موفقیت ساخته شد. خوش آمدید!');
    }
}