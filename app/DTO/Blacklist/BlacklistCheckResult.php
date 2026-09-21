<?php

namespace App\DTO\Blacklist;

use App\Enums\BlacklistSeverity;

readonly class BlacklistCheckResult
{
    public function __construct(
        public bool $passed,
        public ?string $matchedWord = null,
        public ?BlacklistSeverity $severity = null,
    ) {}
}