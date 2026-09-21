<?php

namespace App\Models;

use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'body',
        'channel',
        'status',
        'rejection_reason',
        'ai_categories',
        'ai_raw_response',
        'retry_count',
        'processed_at',
        'cost',
    ];

    protected $casts = [
        'status' => ContentStatus::class,
        'ai_categories' => 'array',
        'ai_raw_response' => 'array',
        'processed_at' => 'datetime',
        'cost' => 'decimal:2',
    ];

    /**
     * کاربری که این محتوا رو ارسال کرده
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * تاریخچه‌ی کامل لایه‌های بررسی این محتوا
     */
    public function logs(): HasMany
    {
        return $this->hasMany(ModerationLog::class);
    }

    /**
     * فیلتر بر اساس یک وضعیت مشخص
     * استفاده: Content::status(ContentStatus::Approved)->get()
     */
    public function scopeStatus(Builder $query, ContentStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * فیلتر محتواهای در انتظار بررسی
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ContentStatus::Pending);
    }

    /**
     * آیا این محتوا قابلیت تلاش مجدد داره؟
     * فقط محتواهایی که status=failed دارن و کمتر از ۳ بار retry شدن
     */
    public function isRetryable(): bool
    {
        return $this->status === ContentStatus::Failed && $this->retry_count < 3;
    }
}