<?php

namespace App\Actions\Wallet;

use App\Models\Content;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\Wallet\WalletService;

class DeductCreditAction
{
    public function __construct(
        private readonly WalletService $walletService,
    ) {}

    public function execute(User $user, Content $content): WalletTransaction
    {
        $cost = config('moderation.cost_per_request', 1000);

        return $this->walletService->debit(
            wallet: $user->wallet,
            amount: $cost,
            description: "هزینه بررسی محتوا #{$content->id}",
            reference: $content,
        );
    }
}