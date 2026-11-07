<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\Wallet\TopUpWalletAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopUpWalletRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::query()->with('wallet')->latest()->paginate(15);

        return UserResource::collection($users)->response();
    }

    public function topUp(TopUpWalletRequest $request, User $user, TopUpWalletAction $action): JsonResponse
    {
        $action->execute($user, (int) $request->validated('amount'));

        $user->load('wallet');

        return response()->json([
            'message' => "حساب کاربر {$user->name} شارژ شد.",
            'user' => UserResource::make($user),
        ]);
    }
}
