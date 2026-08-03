<?php

namespace App\Enums;

enum ModerationResult: string
{
    case Passed = 'passed';
    case Rejected = 'rejected';
    case Error = 'error';

    public function label(): string
    {
        return match ($this) {
            self::Passed => 'قبول شد',
            self::Rejected => 'رد شد',
            self::Error => 'خطا',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Passed => 'green',
            self::Rejected => 'red',
            self::Error => 'gray',
        };
    }
}