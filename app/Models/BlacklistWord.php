<?php

namespace App\Models;

use App\Enums\BlacklistSeverity;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['word', 'severity'])]
class BlacklistWord extends Model
{
    /** @use HasFactory<\Database\Factories\BlacklistWordFactory> */
    use HasFactory;

    protected $casts = [
        'severity' => BlacklistSeverity::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (BlacklistWord $entry): void {
            $entry->word = self::normalizeWord($entry->word);
        });
    }

    public static function normalizeWord(string $word): string
    {
        $word = trim($word);
        $word = preg_replace('/\x{200c}|\x{200d}|\x{feff}/u', '', $word) ?? $word;

        return $word;
    }
}
