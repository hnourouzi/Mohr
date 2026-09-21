<?php

namespace App\Jobs;

use App\Actions\Wallet\RefundCreditAction;
use App\Enums\ContentStatus;
use App\Enums\ModerationLayer;
use App\Enums\ModerationResult;
use App\Events\ContentModerated;
use App\Models\Content;
use App\Models\ModerationLog;
use App\Services\AiModeration\AiModerationServiceInterface;
use App\Services\Content\ContentStatusTransitionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class RunAiModerationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    public function __construct(
        public readonly Content $content,
    ) {
        $this->onQueue('moderation-ai');
    }

    public function handle(
        AiModerationServiceInterface $aiModerationService,
        ContentStatusTransitionService $transitionService,
    ): void {
        $startedAt = microtime(true);

        $result = $aiModerationService->moderate($this->content->body);

        $durationMs = (int) ((microtime(true) - $startedAt) * 1000);

        $newStatus = $result->flagged ? ContentStatus::Rejected : ContentStatus::Approved;

        ModerationLog::create([
            'content_id' => $this->content->id,
            'layer' => ModerationLayer::Layer2Ai,
            'result' => $result->flagged ? ModerationResult::Rejected : ModerationResult::Passed,
            'detail' => $result->flagged ? ['categories' => $result->categories] : null,
            'duration_ms' => $durationMs,
        ]);

        $transitionService->transition($this->content, $newStatus);

        $this->content->update([
            'ai_categories' => $result->categories,
            'ai_raw_response' => $result->rawResponse,
            'rejection_reason' => $result->flagged
                ? 'رد شده توسط بررسی هوش مصنوعی: ' . implode(', ', $result->categories)
                : null,
        ]);

        event(new ContentModerated($this->content));
    }

    /**
     * when the job fails
     */
    public function failed(Throwable $exception): void
    {
        ModerationLog::create([
            'content_id' => $this->content->id,
            'layer' => ModerationLayer::Layer2Ai,
            'result' => ModerationResult::Error,
            'detail' => ['error' => $exception->getMessage()],
            'duration_ms' => null,
        ]);

        app(ContentStatusTransitionService::class)->transition($this->content, ContentStatus::Failed);

        // refund credit because the processing was not completed
        app(RefundCreditAction::class)->execute($this->content);
    }
}