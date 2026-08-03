<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Models\Content;
use App\Models\ModerationLog;
use App\Enums\ModerationResult;
use App\Enums\ContentStatus;
use App\Enums\ModerationLayer;
use App\Enums\WalletTransactionType;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create()->each(function (User $user) {

            $wallet = Wallet::factory()->create([
                'user_id' => $user->id,
                'balance' => 0,
            ]);

            $initialTopUp = Faker::create()->numberBetween(5000, 20000);

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => WalletTransactionType::Credit->value,
                'amount' => $initialTopUp,
                'balance_after' => $initialTopUp,
                'reference_type' => fake()->randomElement(['wallet', 'content']),
                'reference_id' => fake()->numberBetween(1, 100000),
                'description' => 'Initial top up',
            ]);

            $wallet->update(['balance' => $initialTopUp]);

            Content::factory(Faker::create()->numberBetween(2, 5))
                ->create(['user_id' => $user->id])
                ->each(function (Content $content) use ($wallet) {
                    $this->attachModerationTrail($content, $wallet);
                });
        });

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            BlacklistWordSeeder::class,
        ]);
    }


    private function attachModerationTrail(Content $content, Wallet $wallet): void
    {

        ModerationLog::create([
            'content_id' => $content->id,
            'layer' => ModerationLayer::Layer1Blacklist->value,
            'result' => $content->status === ContentStatus::Layer1Rejected
                ? ModerationResult::Rejected
                : ModerationResult::Passed,
            'detail' => $content->status === ContentStatus::Layer1Rejected
                ? ['matched_word' => fake()->word()]
                : null,
            'duration_ms' => rand(5, 50),
        ]);

        if ($content->status !== ContentStatus::Layer1Rejected) {
            ModerationLog::create([
                'content_id' => $content->id,
                'layer' => ModerationLayer::Layer2Ai->value,
                'result' => match ($content->status) {
                    ContentStatus::Approved => ModerationResult::Passed,
                    ContentStatus::Rejected, ContentStatus::Flagged => ModerationResult::Rejected,
                    ContentStatus::Failed => ModerationResult::Error,
                    default => ModerationResult::Passed,
                },
                'detail' => $content->status === ContentStatus::Rejected
                    ? ['category' => fake()->randomElement(['hate', 'violence', 'harassment']), 'score' => fake()->randomFloat(2, 0.7, 0.99)]
                    : null,
                'duration_ms' => rand(200, 1200),
            ]);
        }


        if ($content->status !== ContentStatus::Pending) {
            $cost = config('moderation.cost_per_request', 1000);

            $newBalance = $wallet->fresh()->balance - $cost;

            WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => WalletTransactionType::Debit->value,
                'amount' => $cost,
                'balance_after' => max($newBalance, 0),
                'reference_type' => Content::class,
                'reference_id' => $content->id,
                'description' => 'Content moderation cost #' . $content->id,
            ]);

            $wallet->decrement('balance', $cost);
        }
    }
}
