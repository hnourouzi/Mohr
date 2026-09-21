<?php

namespace App\Services\AiModeration;

use App\DTO\AiModeration\AiModerationResult;

interface AiModerationServiceInterface
{
    public function moderate(string $body): AiModerationResult;
}