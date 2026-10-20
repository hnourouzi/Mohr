<?php

namespace App\Jobs;

use App\Actions\Content\RunLayer1CheckAction;
use App\Actions\Wallet\RefundCreditAction;
use App\Enums\ContentStatus;
use App\Enums\ModerationLayer;
use App\Enums\ModerationResult;
use App\Models\Content;
use App\Models\ModerationLog;
use App\Services\Content\ContentStatusTransitionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ModerateContentJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $uniqueFor = 3600;

    public function __construct(
        public readonly Content $content,
    ) {
        $this->onQueue('moderation');
    }

    public function uniqueId(): string
    {
        return 'moderate-content-'.$this->content->id;
    }

    public function handle(RunLayer1CheckAction $runLayer1CheckAction): void
    {
        $content = Content::query()->findOrFail($this->content->id);

        $passed = $runLayer1CheckAction->execute($content);

        if ($passed) {
            RunAiModerationJob::dispatch($content)
                ->onQueue('moderation-ai')
                ->afterCommit();
        }
    }

    public function failed(Throwable $exception): void
    {
        $content = Content::query()->find($this->content->id);

        if (! $content) {
            return;
        }

        if (in_array($content->status, [
            ContentStatus::Approved,
            ContentStatus::Rejected,
            ContentStatus::Layer1Rejected,
            ContentStatus::Flagged,
            ContentStatus::Failed,
        ], true)) {
            return;
        }

        ModerationLog::create([
            'content_id' => $content->id,
            'layer' => ModerationLayer::Layer1Blacklist,
            'result' => ModerationResult::Error,
            'detail' => ['error' => 'layer1_system_failure'],
            'duration_ms' => null,
        ]);

        $transition = app(ContentStatusTransitionService::class);

        if ($transition->canTransition($content->status, ContentStatus::Failed)) {
            $transition->transition($content, ContentStatus::Failed);
        }

        app(RefundCreditAction::class)->execute($content);
    }
}
