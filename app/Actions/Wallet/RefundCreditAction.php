<?php

namespace App\Actions\Wallet;

use App\Models\Content;
use App\Services\Wallet\WalletService;

class RefundCreditAction
{
    public function __construct(
        private readonly WalletService $walletService,
    ) {}

    public function execute(Content $content): void
    {
        $this->walletService->credit(
            wallet: $content->user->wallet,
            amount: $content->cost,
            description: "بازگشت وجه به دلیل خطا در بررسی محتوا #{$content->id}",
            reference: $content,
        );
    }
}