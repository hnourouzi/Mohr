<?php

namespace App\Enums;

enum WalletTransactionType: string
{
    case Credit = 'credit';
    case Debit = 'debit';

    public function label(): string
    {
        return match ($this) {
            self::Credit => 'واریز',
            self::Debit => 'برداشت',
        };
    }

    public function sign(): string
    {
        return match ($this) {
            self::Credit => '+',
            self::Debit => '-',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Credit => 'green',
            self::Debit => 'red',
        };
    }
}