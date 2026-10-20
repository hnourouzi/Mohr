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

        $words = BlacklistWord::query()
            ->get(['word', 'severity'])
            ->sortByDesc(fn (BlacklistWord $entry) => mb_strlen($entry->word))
            ->values();

        $blockMatch = null;
        $flagMatch = null;

        foreach ($words as $entry) {
            $word = $entry->word;
            $normalizedWord = mb_strtolower($word);

            if (! $this->containsWholeWord($normalizedBody, $normalizedWord)) {
                continue;
            }

            $severity = $entry->severity instanceof BlacklistSeverity
                ? $entry->severity
                : BlacklistSeverity::from((string) $entry->severity);

            if ($severity === BlacklistSeverity::Block) {
                $blockMatch = $word;

                break;
            }

            $flagMatch ??= $word;
        }

        if ($blockMatch !== null) {
            return new BlacklistCheckResult(
                passed: false,
                matchedWord: $blockMatch,
                severity: BlacklistSeverity::Block,
            );
        }

        if ($flagMatch !== null) {
            return new BlacklistCheckResult(
                passed: false,
                matchedWord: $flagMatch,
                severity: BlacklistSeverity::Flag,
            );
        }

        return new BlacklistCheckResult(passed: true);
    }

    /**
     * Match blacklist terms as whole words/tokens, not arbitrary substrings.
     */
    private function containsWholeWord(string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return false;
        }

        $pattern = '/(?<![\p{L}\p{N}])'
            .preg_quote($needle, '/')
            .'(?![\p{L}\p{N}])/iu';

        return preg_match($pattern, $haystack) === 1;
    }
}
