<?php

namespace App\Services\Blacklist;

use App\DTO\Blacklist\BlacklistCheckResult;
use App\Enums\BlacklistSeverity;
use App\Models\BlacklistWord;

class BlacklistCheckService
{
    public function check(string $body): BlacklistCheckResult
    {
        $normalizedBody = mb_strtolower($body);

        $words = BlacklistWord::pluck('severity', 'word');

        foreach ($words as $word => $severity) {
            if (str_contains($normalizedBody, mb_strtolower($word))) {
                return new BlacklistCheckResult(
                    passed: $severity !== BlacklistSeverity::Block->value,
                    matchedWord: $word,
                    severity: BlacklistSeverity::from($severity),
                );
            }
        }

        return new BlacklistCheckResult(passed: true);
    }
}