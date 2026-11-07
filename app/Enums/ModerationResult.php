<?php

namespace App\Enums;

enum ModerationResult: string
{
    case Passed = 'passed';
    case Rejected = 'rejected';
    case Flagged = 'flagged';
    case Error = 'error';
    case Warning = 'warning';

    public function label(): string
    {
        return match ($this) {
            self::Passed => 'قبول شد',
            self::Rejected => 'رد شد',
            self::Flagged => 'علامت‌گذاری شد',
            self::Error => 'خطا',
            self::Warning => 'هشدار',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Passed => 'green',
            self::Rejected => 'red',
            self::Flagged => 'yellow',
            self::Error => 'gray',
            self::Warning => 'yellow',
        };
    }
}