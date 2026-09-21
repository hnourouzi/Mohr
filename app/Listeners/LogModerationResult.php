<?php

namespace App\Listeners;

use App\Events\ContentModerated;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogModerationResult implements ShouldQueue
{
    public function handle(ContentModerated $event): void
    {
        logger()->info('Content moderated', [
            'content_id' => $event->content->id,
            'status' => $event->content->status->value,
        ]);

    }
}