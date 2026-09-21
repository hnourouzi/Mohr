<?php

namespace App\Actions\Content;

use App\Enums\ContentStatus;
use App\Exceptions\InvalidStatusTransitionException;
use App\Jobs\ModerateContentJob;
use App\Models\Content;
use App\Services\Content\ContentStatusTransitionService;

class RetryModerationAction
{
    public function __construct(
        private readonly ContentStatusTransitionService $transitionService,
    ) {}

    public function execute(Content $content): void
    {
        if (! $content->isRetryable()) {
            throw new InvalidStatusTransitionException($content->status, ContentStatus::Processing);
        }

        $content->increment('retry_count');

        $this->transitionService->transition($content, ContentStatus::Processing);

        ModerateContentJob::dispatch($content);
    }
}