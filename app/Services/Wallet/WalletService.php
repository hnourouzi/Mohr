<?php

namespace App\Services\Wallet;

use App\Enums\WalletTransactionType;
use App\Exceptions\InsufficientBalanceException;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /**
     * Credit amount to wallet
     * @throws InsufficientBalanceException
     * @param Wallet $wallet
     * @param float $amount
     * @param string $description
     * @param mixed $reference
     * @return WalletTransaction
     */
    public function credit(Wallet $wallet, float $amount, string $description, ?Model $reference = null): WalletTransaction
    {
        return DB::transaction(function () use ($wallet, $amount, $description, $reference) {
            $lockedWallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();

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
     * Debit amount from wallet
     * @throws InsufficientBalanceException
     * @param Wallet $wallet
     * @param float $amount
     * @param string $description
     * @param mixed $reference
     * @return WalletTransaction
     */
    public function debit(Wallet $wallet, float $amount, string $description, ?Model $reference = null): WalletTransaction
    {
        return DB::transaction(function () use ($wallet, $amount, $description, $reference) {
            $lockedWallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();

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
     * Record transaction
     * @param Wallet $wallet
     * @param WalletTransactionType $type
     * @param float $amount
     * @param float $balanceAfter
     * @param string $description
     * @param mixed $reference
     * @return WalletTransaction
     */
    private function recordTransaction(Wallet $wallet, WalletTransactionType $type, float $amount, float $balanceAfter, string $description, ?Model $reference)
    {
        return WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'type' => $type,
            'amount' => $amount,
            'balance_after' => $balanceAfter,
            'description' => $description,
            'reference_id' => $reference?->getKey(),
            'reference_type' => $reference?->getMorphClass() ?? ($reference ? get_class($reference) : null),
        ]);
    }
}