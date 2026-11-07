<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Auth\RegisterUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Auth\ApiTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function register(
        RegisterUserRequest $request,
        RegisterUserAction $registerUserAction,
        ApiTokenService $tokenService,
    ): JsonResponse {
        $user = $registerUserAction->execute(
            name: $request->validated('name'),
            email: $request->validated('email'),
            password: $request->validated('password'),
            dispatchRegisteredEvent: false,
        );

        $user->load('wallet');
        $token = $tokenService->createToken($user, $request->header('User-Agent'));

        return $this->tokenResponse($user, $token->plainTextToken, $token->accessToken->expires_at?->toIso8601String())
            ->setStatusCode(201);
    }

    public function login(LoginRequest $request, ApiTokenService $tokenService): JsonResponse
    {
        $user = User::query()->where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['اطلاعات ورود نادرست است.'],
            ]);
        }

        $user->load('wallet');
        $token = $tokenService->createToken($user, $request->validated('device_name'));

        return $this->tokenResponse($user, $token->plainTextToken, $token->accessToken->expires_at?->toIso8601String());
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $accessToken = $user->currentAccessToken();

        if ($accessToken instanceof PersonalAccessToken) {
            $accessToken->delete();
        } elseif ($bearer = $request->bearerToken()) {
            $tokenId = explode('|', $bearer, 2)[0] ?? null;

            if ($tokenId) {
                $user->tokens()->whereKey($tokenId)->delete();
            }
        }

        return response()->json(['message' => 'خروج با موفقیت انجام شد.']);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('wallet');

        return response()->json([
            'user' => UserResource::make($user),
            'wallet' => [
                'balance' => $user->wallet->balance,
            ],
        ]);
    }

    private function tokenResponse(User $user, string $plainTextToken, ?string $expiresAt): JsonResponse
    {
        return response()->json([
            'user' => UserResource::make($user),
            'token' => $plainTextToken,
            'token_type' => 'Bearer',
            'expires_at' => $expiresAt,
        ]);
    }
}
