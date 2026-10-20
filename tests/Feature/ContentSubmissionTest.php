<?php

namespace Tests\Feature;

use App\Enums\ContentStatus;
use App\Enums\WalletTransactionType;
use App\Jobs\ModerateContentJob;
use App\Models\Content;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ContentSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_submission_debits_wallet_and_dispatches_job(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $user->wallet->update(['balance' => 5000]);

        $response = $this->actingAs($user)->post(route('panel.contents.store'), [
            'body' => 'متن آزمایشی برای بررسی',
            'channel' => 'web',
        ]);

        $response->assertRedirect();

        $content = Content::first();
        $this->assertNotNull($content);
        $this->assertSame(ContentStatus::Pending, $content->status);

        $this->assertSame(4000, $user->wallet->fresh()->balance);

        $this->assertDatabaseHas('wallet_transactions', [
            'wallet_id' => $user->wallet->id,
            'type' => WalletTransactionType::Debit->value,
            'amount' => 1000,
            'reference_id' => $content->id,
        ]);

        Queue::assertPushed(ModerateContentJob::class);
    }

    public function test_insufficient_balance_prevents_content_and_job(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $user->wallet->update(['balance' => 100]);

        $response = $this->actingAs($user)->from(route('panel.contents.create'))
            ->post(route('panel.contents.store'), [
                'body' => 'متن آزمایشی برای بررسی',
            ]);

        $response->assertRedirect(route('panel.contents.create'));
        $response->assertSessionHasErrors('balance');

        $this->assertDatabaseCount('contents', 0);
        Queue::assertNothingPushed();
    }
}
