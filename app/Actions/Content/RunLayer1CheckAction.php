<?php

namespace App\Actions\Content;

use App\Enums\ContentStatus;
use App\Enums\ModerationLayer;
use App\Enums\ModerationResult;
use App\Models\Content;
use App\Models\ModerationLog;
use App\Services\Blacklist\BlacklistCheckService;
use App\Services\Content\ContentStatusTransitionService;

class RunLayer1CheckAction
{
    public function __construct(
        private readonly BlacklistCheckService $blacklistCheckService,
        private readonly ContentStatusTransitionService $transitionService,
    ) {}

    public function execute(Content $content): bool
    {
        $startedAt = microtime(true);

        $result = $this->blacklistCheckService->check($content->body);

        $durationMs = (int) ((microtime(true) - $startedAt) * 1000);

        ModerationLog::create([
            'content_id' => $content->id,
            'layer' => ModerationLayer::Layer1Blacklist,
            'result' => $result->passed ? ModerationResult::Passed : ModerationResult::Rejected,
            'detail' => $result->matchedWord ? ['matched_word' => $result->matchedWord] : null,
            'duration_ms' => $durationMs,
        ]);

        if (! $result->passed) {
            $this->transitionService->transition($content, ContentStatus::Layer1Rejected);
            $content->update(['rejection_reason' => "کلمه‌ی نامناسب یافت شد: {$result->matchedWord}"]);

            return false;
        }

        $this->transitionService->transition($content, ContentStatus::Processing);

        return true;
    }
}