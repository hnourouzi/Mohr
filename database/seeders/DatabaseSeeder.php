<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Enums\ModerationLayer;
use App\Enums\ModerationResult;
use App\Enums\WalletTransactionType;
use App\Models\Content;
use App\Models\ModerationLog;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'مدیر سیستم',
            'email' => 'admin@example.com',
        ]);
        $this->createUserWithWallet($admin, 10000, 50000);

        $apiUser = User::factory()->create([
            'name' => 'کاربر API',
            'email' => 'user@example.com',
        ]);
        $apiUser->wallet->update(['balance' => 50000]);

        User::factory(10)->create()->each(function (User $user) {
            $this->createUserWithWallet($user, 5000, 20000);
        });

        $this->call([
            BlacklistWordSeeder::class,
        ]);
    }

    private function createUserWithWallet(User $user, int $minTopUp, int $maxTopUp): void
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        if ($wallet->wasRecentlyCreated) {
            $initialTopUp = fake()->numberBetween($minTopUp, $maxTopUp);

            DB::transaction(function () use ($wallet, $initialTopUp) {
                $wallet->update([
                    'balance' => $initialTopUp,
                ]);

                WalletTransaction::create([
                    'wallet_id' => $wallet->id,
                    'type' => WalletTransactionType::Credit->value,
                    'amount' => $initialTopUp,
                    'balance_after' => $initialTopUp,
                    'description' => 'Initial top up',
                ]);
            });
        }

        Content::factory(fake()->numberBetween(2, 5))
            ->create(['user_id' => $user->id])
            ->each(function (Content $content) use ($wallet) {
                $this->attachModerationTrail($content, $wallet);
            });
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
            $cost = (int) config('moderation.cost_per_request', 1000);

            DB::transaction(function () use ($wallet, $content, $cost) {
                $locked = Wallet::query()->whereKey($wallet->id)->lockForUpdate()->firstOrFail();
                $newBalance = max($locked->balance - $cost, 0);

                WalletTransaction::create([
                    'wallet_id' => $locked->id,
                    'type' => WalletTransactionType::Debit->value,
                    'amount' => $cost,
                    'balance_after' => $newBalance,
                    'reference_type' => $content->getMorphClass(),
                    'reference_id' => $content->id,
                    'description' => 'Content moderation cost #'.$content->id,
                ]);

                $locked->update(['balance' => $newBalance]);
            });
        }
    }
}
