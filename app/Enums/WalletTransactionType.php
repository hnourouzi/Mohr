<?php

namespace App\Enums;

enum WalletTransactionType: string
{
    case Credit = 'credit';
    case Debit = 'debit';
    case Refund = 'refund';

    public function label(): string
    {
        return match ($this) {
            self::Credit => 'واریز',
            self::Debit => 'برداشت',
            self::Refund => 'بازگشت وجه',
        };
    }

    public function sign(): string
    {
        return match ($this) {
            self::Credit, self::Refund => '+',
            self::Debit => '-',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Credit, self::Refund => 'green',
            self::Debit => 'red',
        };
    }
}