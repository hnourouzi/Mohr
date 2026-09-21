<?php

namespace App\Actions\Wallet;

use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\Wallet\WalletService;

class TopUpWalletAction
{
    public function __construct(
        private readonly WalletService $walletService,
    ) {}

    public function execute(User $user, float $amount): WalletTransaction
    {
        return $this->walletService->credit(
            wallet: $user->wallet,
            amount: $amount,
            description: 'شارژ حساب کاربری',
        );
    }
}