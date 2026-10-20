<?php

namespace App\Services\Wallet;

use App\Enums\WalletTransactionType;
use App\Exceptions\InsufficientBalanceException;
use App\Models\Content;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class WalletService
{
    public function credit(Wallet $wallet, int $amount, string $description, ?Model $reference = null): WalletTransaction
    {
        return DB::transaction(function () use ($wallet, $amount, $description, $reference) {
            $lockedWallet = Wallet::query()->whereKey($wallet->id)->lockForUpdate()->firstOrFail();

            $newBalance = $lockedWallet->balance + $amount;
            $lockedWallet->update(['balance' => $newBalance]);

            return $this->recordTransaction(
                wallet: $lockedWallet,
                type: WalletTransactionType::Credit,
                amount: $amount,
                balanceAfter: $newBalance,
                description: $description,
                reference: $reference,
            );
        });
    }

    /**
     * @throws InsufficientBalanceException
     */
    public function debit(Wallet $wallet, int $amount, string $description, ?Model $reference = null): WalletTransaction
    {
        return DB::transaction(function () use ($wallet, $amount, $description, $reference) {
            $lockedWallet = Wallet::query()->whereKey($wallet->id)->lockForUpdate()->firstOrFail();

            if ($lockedWallet->balance < $amount) {
                throw new InsufficientBalanceException();
            }

            $newBalance = $lockedWallet->balance - $amount;
            $lockedWallet->update(['balance' => $newBalance]);

            return $this->recordTransaction(
                wallet: $lockedWallet,
                type: WalletTransactionType::Debit,
                amount: $amount,
                balanceAfter: $newBalance,
                description: $description,
                reference: $reference,
            );
        });
    }

    /**
     * Idempotent refund for a moderated content charge (at most one refund per content).
     */
    public function refundForContent(Wallet $wallet, Content $content, string $description): ?WalletTransaction
    {
        try {
            return DB::transaction(function () use ($wallet, $content, $description) {
                $existing = WalletTransaction::query()
                    ->where('reference_type', $content->getMorphClass())
                    ->where('reference_id', $content->getKey())
                    ->where('type', WalletTransactionType::Refund)
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    return $existing;
                }

                $amount = (int) round((float) $content->cost);
                if ($amount <= 0) {
                    return null;
                }

                $lockedWallet = Wallet::query()->whereKey($wallet->id)->lockForUpdate()->firstOrFail();
                $newBalance = $lockedWallet->balance + $amount;
                $lockedWallet->update(['balance' => $newBalance]);

                return $this->recordTransaction(
                    wallet: $lockedWallet,
                    type: WalletTransactionType::Refund,
                    amount: $amount,
                    balanceAfter: $newBalance,
                    description: $description,
                    reference: $content,
                );
            });
        } catch (QueryException $e) {
            if ($this->isUniqueConstraintViolation($e)) {
                return WalletTransaction::query()
                    ->where('reference_type', $content->getMorphClass())
                    ->where('reference_id', $content->getKey())
                    ->where('type', WalletTransactionType::Refund)
                    ->first();
            }

            throw $e;
        }
    }

    private function recordTransaction(
        Wallet $wallet,
        WalletTransactionType $type,
        int $amount,
        int $balanceAfter,
        string $description,
        ?Model $reference,
    ): WalletTransaction {
        return WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => $type,
            'amount' => $amount,
            'balance_after' => $balanceAfter,
            'description' => $description,
            'reference_id' => $reference?->getKey(),
            'reference_type' => $reference ? $reference->getMorphClass() : null,
        ]);
    }

    private function isUniqueConstraintViolation(QueryException $e): bool
    {
        $code = $e->errorInfo[1] ?? null;

        return in_array($code, [1062, 19], true);
    }
}
