<?php

namespace App\Models;

use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'body', 'status', 'rejection_reason', 'ai_categories', 'ai_row_response', 'retry_count', 'processed_at'])]

class Content extends Model
{
    /** @use HasFactory<\Database\Factories\ContentFactory> */
    use HasFactory;
    protected $casts = [
        'status'          => ContentStatus::class,
        'ai_categories'   => 'array',
        'ai_raw_response' => 'array',
        'processed_at'    => 'datetime',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
