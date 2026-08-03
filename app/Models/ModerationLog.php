<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\ModerationLayer;
use App\Enums\ModerationResult;

#[Fillable(['content_id', 'layer', 'result', 'detail', 'duration_ms'])]
class ModerationLog extends Model
{
    /** @use HasFactory<\Database\Factories\ModerationLogFactory> */
    use HasFactory;

    protected $casts = [
        'layer' => ModerationLayer::class,
        'result' => ModerationResult::class,
        'detail' => 'array',
    ];

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }
}
