<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'channel' => $this->channel,
            'rejection_reason' => $this->rejection_reason,
            'cost' => $this->cost,
            'processed_at' => $this->processed_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'logs' => ModerationLogResource::collection($this->whenLoaded('logs')),
        ];
    }
}
