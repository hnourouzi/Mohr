<?php

namespace App\Jobs;

use App\Actions\Content\RunLayer1CheckAction;
use App\Models\Content;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ModerateContentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1; // خود لایه ۱ نباید retry بشه؛ retry مال لایه AI هست

    public function __construct(
        public readonly Content $content,
    ) {
        $this->onQueue('moderation');
    }

    public function handle(RunLayer1CheckAction $runLayer1CheckAction): void
    {
        $passed = $runLayer1CheckAction->execute($this->content);

        if ($passed) {
            RunAiModerationJob::dispatch($this->content)
                ->onQueue('moderation-ai');
        }
    }
}