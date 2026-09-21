<?php

namespace App\Events;

use App\Models\Content;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContentModerated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Content $content,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->content->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'content_id' => $this->content->id,
            'status' => $this->content->status->value,
            'status_label' => $this->content->status->label(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'content.moderated';
    }
}