<?php

namespace App\Enums;

enum BlacklistSeverity: string
{
    case Block = 'block';
    case Flag = 'flag';

    public function label(): string
    {
        return match ($this) {
            self::Block => 'مسدود کننده',
            self::Flag => 'فقط علامت‌گذاری',
        };
    }
}