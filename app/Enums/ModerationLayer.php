<?php

namespace App\Enums;

enum ModerationLayer: string
{
    case Layer1Blacklist = 'layer1_blacklist';
    case Layer2Ai = 'layer2_ai';

    public function label(): string
    {
        return match ($this) {
            self::Layer1Blacklist => 'فیلتر کلمات ممنوعه',
            self::Layer2Ai => 'بررسی هوش مصنوعی',
        };
    }
}