<?php

namespace App\Jobs;

use App\Actions\Wallet\RefundCreditAction;
use App\Enums\ContentStatus;
use App\Enums\ModerationLayer;
use App\Enums\ModerationResult;
use App\Enums\ModerationVerdict;
use App\Events\ContentModerated;
use App\Models\Content;
use App\Models\ModerationLog;
use App\Services\AiModeration\AiModerationServiceInterface;
use App\Services\Content\ContentStatusTransitionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class RunAiModerationJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries;

    public array $backoff;

    public int $timeout;

    public int $uniqueFor = 3600;

    public function __construct(
        public readonly Content $content,
    ) {
        $this->onQueue('moderation-ai');
        $this->tries = (int) config('moderation.ai_job.tries', 3);
        $this->backoff = config('moderation.ai_job.backoff', [10, 30, 60]);
        $this->timeout = (int) config('moderation.openrouter.timeout', 30) + 15;
    }

    public function uniqueId(): string
    {
        return 'moderate-ai-content-'.$this->content->id;
    }

    public function handle(
        AiModerationServiceInterface $aiModerationService,
        ContentStatusTransitionService $transitionService,
    ): void {
        $content = Content::query()->findOrFail($this->content->id);

        if ($content->status !== ContentStatus::Processing) {
            return;
        }

        $startedAt = microtime(true);

        $result = $aiModerationService->moderate($content->body);

        $durationMs = (int) ((microtime(true) - $startedAt) * 1000);

        $verdict = $result->verdict;
        $isApproved = $verdict === ModerationVerdict::Approve;

        ModerationLog::create([
            'content_id' => $content->id,
            'layer' => ModerationLayer::Layer2Ai,
            'result' => $verdict->toLogResult(),
            'detail' => $isApproved
                ? null
                : ['categories' => $result->categories, 'reason' => $result->reason],
            'duration_ms' => $durationMs,
        ]);

        $transitionService->transition($content, $verdict->toContentStatus());

        $content->update([
            'ai_categories' => $result->categories,
            'ai_raw_response' => $result->sanitizedRawResponse(),
            'rejection_reason' => $isApproved ? null : $this->buildReason($result->verdict, $result->reason, $result->categories),
        ]);

        event(new ContentModerated($content));
    }

    /**
     * @param  list<string>  $categories
     */
    private function buildReason(ModerationVerdict $verdict, ?string $reason, array $categories): string
    {
        $prefix = $verdict === ModerationVerdict::Reject
            ? 'رد شده توسط بررسی هوش مصنوعی'
            : 'نیازمند بررسی انسانی';

        $parts = array_filter([
            $reason,
            $categories !== [] ? implode(', ', $categories) : null,
        ]);

        return $prefix.($parts !== [] ? ': '.implode(' — ', $parts) : '');
    }

    public function failed(Throwable $exception): void
    {
        $content = Content::query()->find($this->content->id);

        if (! $content || $content->status !== ContentStatus::Processing) {
            return;
        }

        ModerationLog::create([
            'content_id' => $content->id,
            'layer' => ModerationLayer::Layer2Ai,
            'result' => ModerationResult::Error,
            'detail' => ['error' => 'ai_provider_failure'],
            'duration_ms' => null,
        ]);

        app(ContentStatusTransitionService::class)->transition($content, ContentStatus::Failed);

        app(RefundCreditAction::class)->execute($content);
    }
}