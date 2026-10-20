<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Wallet\TopUpWalletAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopUpWalletRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('wallet')->latest()->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->load(['contents', 'wallet.transactions']);

        return view('admin.users.show', compact('user'));
    }

    public function topUp(TopUpWalletRequest $request, User $user, TopUpWalletAction $action): RedirectResponse
    {
        $action->execute($user, (int) $request->validated('amount'));

        return back()->with('success', "حساب کاربر {$user->name} شارژ شد.");
    }
}