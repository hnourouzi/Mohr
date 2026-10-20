<?php

namespace App\Actions\Content;

use App\Enums\BlacklistSeverity;
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

        if ($result->severity === BlacklistSeverity::Block) {
            ModerationLog::create([
                'content_id' => $content->id,
                'layer' => ModerationLayer::Layer1Blacklist,
                'result' => ModerationResult::Rejected,
                'detail' => ['matched_word' => $result->matchedWord],
                'duration_ms' => $durationMs,
            ]);

            $this->transitionService->transition($content, ContentStatus::Layer1Rejected);
            $content->update(['rejection_reason' => "کلمه‌ی نامناسب یافت شد: {$result->matchedWord}"]);

            return false;
        }

        if ($result->severity === BlacklistSeverity::Flag) {
            ModerationLog::create([
                'content_id' => $content->id,
                'layer' => ModerationLayer::Layer1Blacklist,
                'result' => ModerationResult::Flagged,
                'detail' => ['matched_word' => $result->matchedWord],
                'duration_ms' => $durationMs,
            ]);

            $this->transitionService->transition($content, ContentStatus::Flagged);
            $content->update([
                'rejection_reason' => "نیاز به بررسی انسانی (کلمهٔ علامت‌گذاری‌شده: {$result->matchedWord})",
            ]);

            return false;
        }

        ModerationLog::create([
            'content_id' => $content->id,
            'layer' => ModerationLayer::Layer1Blacklist,
            'result' => ModerationResult::Passed,
            'detail' => null,
            'duration_ms' => $durationMs,
        ]);

        if ($content->status === ContentStatus::Pending) {
            $this->transitionService->transition($content, ContentStatus::Processing);
        }

        return true;
    }
}
