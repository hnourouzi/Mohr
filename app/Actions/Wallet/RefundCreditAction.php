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
        $content->loadMissing('user.wallet');

        if (! $content->user?->wallet) {
            return;
        }

        $this->walletService->refundForContent(
            wallet: $content->user->wallet,
            content: $content,
            description: "بازگشت وجه به دلیل خطا در بررسی محتوا #{$content->id}",
        );
    }
}
