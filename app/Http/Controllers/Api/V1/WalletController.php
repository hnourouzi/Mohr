<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Wallet\SimulateWalletTopUpAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SimulateTopUpWalletRequest;
use App\Http\Resources\WalletTransactionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $wallet = $request->user()->wallet;

        return response()->json([
            'balance' => $wallet->balance,
            'cost_per_request' => (int) config('moderation.cost_per_request', 1000),
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 15), 50);

        $transactions = $request->user()
            ->wallet
            ->transactions()
            ->latest()
            ->paginate($perPage);

        return WalletTransactionResource::collection($transactions)->response();
    }

    public function topUp(
        SimulateTopUpWalletRequest $request,
        SimulateWalletTopUpAction $action,
    ): JsonResponse {
        $transaction = $action->execute($request->user(), (int) $request->validated('amount'));

        return response()->json([
            'message' => 'کیف پول با موفقیت شارژ شد.',
            'balance' => $request->user()->wallet->fresh()->balance,
            'transaction' => WalletTransactionResource::make($transaction),
        ]);
    }
}
