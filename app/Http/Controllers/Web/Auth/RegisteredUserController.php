<?php

namespace App\Http\Controllers\Web\Auth;

use App\Actions\Auth\RegisterUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterUserRequest $request, RegisterUserAction $registerUserAction): RedirectResponse
    {
        $user = $registerUserAction->execute(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
        );

        Auth::login($user);

        return redirect()->route('panel.dashboard')
            ->with('success', 'حساب شما با موفقیت ساخته شد. خوش آمدید!');
    }
}
