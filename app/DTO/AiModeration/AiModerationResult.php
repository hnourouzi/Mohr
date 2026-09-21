<?php

namespace App\DTO\AiModeration;

readonly class AiModerationResult
{
    public function __construct(
        public bool $flagged,
        public array $categories,
        public array $rawResponse,
    ) {}
}