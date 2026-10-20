<?php

namespace Tests\Feature;

use App\Actions\Wallet\RefundCreditAction;
use App\Enums\ContentStatus;
use App\Enums\WalletTransactionType;
use App\Models\Content;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefundIdempotencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_refund_runs_only_once_per_content(): void
    {
        $user = User::factory()->create();
        $user->wallet->update(['balance' => 5000]);

        $content = Content::factory()->create([
            'user_id' => $user->id,
            'status' => ContentStatus::Failed,
            'cost' => 1000,
        ]);

        $action = app(RefundCreditAction::class);

        $action->execute($content);
        $action->execute($content);

        $this->assertSame(6000, $user->wallet->fresh()->balance);

        $this->assertSame(
            1,
            $user->wallet->transactions()
                ->where('type', WalletTransactionType::Refund)
                ->where('reference_id', $content->id)
                ->count()
        );
    }
}
