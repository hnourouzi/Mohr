<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModerationLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'layer' => $this->layer->value,
            'result' => $this->result->value,
            'detail' => $this->detail,
            'duration_ms' => $this->duration_ms,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
