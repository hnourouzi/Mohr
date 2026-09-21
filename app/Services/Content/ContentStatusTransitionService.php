<?php

namespace App\Services\Content;

use App\Enums\ContentStatus;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\Content;

class ContentStatusTransitionService
{
    private const array ALLOWED_TRANSITIONS = [
        'pending' => ['processing', 'layer1_rejected'],
        'processing' => ['approved', 'rejected', 'flagged', 'failed'],
        'failed' => ['processing'],
    ];

    public function canTransition(ContentStatus $from, ContentStatus $to): bool
    {
        return in_array($to->value, self::ALLOWED_TRANSITIONS[$from->value] ?? [], true);
    }

    public function transition(Content $content, ContentStatus $to): void
    {
        $from = $content->status;

        if (! $this->canTransition($from, $to)) {
            throw new InvalidStatusTransitionException($from, $to);
        }

        $content->update([
            'status' => $to,
            'processed_at' => $to->isFinal() ? now() : $content->processed_at,
        ]);
    }
}