<?php

namespace App\Actions\Content;

use App\Enums\ContentStatus;
use App\Exceptions\InvalidStatusTransitionException;
use App\Jobs\ModerateContentJob;
use App\Models\Content;
use App\Services\Content\ContentStatusTransitionService;
use Illuminate\Support\Facades\DB;

class RetryModerationAction
{
    public function __construct(
        private readonly ContentStatusTransitionService $transitionService,
    ) {}

    public function execute(Content $content): void
    {
        DB::transaction(function () use ($content) {
            $locked = Content::query()->whereKey($content->id)->lockForUpdate()->firstOrFail();

            if (! $locked->isRetryable()) {
                throw new InvalidStatusTransitionException($locked->status, ContentStatus::Processing);
            }

            $locked->increment('retry_count');

            $this->transitionService->transition($locked, ContentStatus::Processing);

            ModerateContentJob::dispatch($locked)
                ->onQueue('moderation')
                ->afterCommit();
        });
    }
}
