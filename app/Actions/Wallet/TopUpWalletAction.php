<?php

namespace App\Actions\Wallet;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\Wallet\WalletService;

/**
 * Manual administrator credit adjustment (not a verified payment gateway).
 */
class TopUpWalletAction
{
    public function __construct(
        private readonly WalletService $walletService,
    ) {}

    public function execute(User $user, int $amount): WalletTransaction
    {
        return $this->walletService->credit(
            wallet: $user->wallet,
            amount: $amount,
            description: 'شارژ دستی توسط مدیر',
        );
    }
}
