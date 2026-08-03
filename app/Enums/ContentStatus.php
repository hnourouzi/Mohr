<?php

namespace App\Enums;

enum ContentStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Layer1Rejected = 'layer1_rejected';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Flagged = 'flagged';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'در انتظار بررسی',
            self::Processing => 'در حال پردازش',
            self::Layer1Rejected => 'رد شده (فیلتر اولیه)',
            self::Approved => 'تایید شده',
            self::Rejected => 'رد شده',
            self::Flagged => 'نیاز به بررسی انسانی',
            self::Failed => 'خطا در پردازش',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Approved => 'green',
            self::Rejected, self::Layer1Rejected => 'red',
            self::Flagged => 'yellow',
            self::Failed => 'gray',
            self::Pending, self::Processing => 'blue',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [
            self::Approved,
            self::Rejected,
            self::Layer1Rejected,
            self::Flagged,
        ]);
    }
}