<?php

namespace App\Enums;

enum ModerationResult: string
{
    case Passed = 'passed';
    case Rejected = 'rejected';
    case Flagged = 'flagged';
    case Error = 'error';

    public function label(): string
    {
        return match ($this) {
            self::Passed => 'قبول شد',
            self::Rejected => 'رد شد',
            self::Flagged => 'علامت‌گذاری شد',
            self::Error => 'خطا',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Passed => 'green',
            self::Rejected => 'red',
            self::Flagged => 'yellow',
            self::Error => 'gray',
        };
    }
}