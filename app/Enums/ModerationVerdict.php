<?php

namespace App\Enums;

enum ModerationVerdict: string
{
    case Approve = 'approve';
    case Warn = 'warn';
    case Reject = 'reject';

    /**
     * وضعیت نهایی محتوا بر اساس تصمیم هوش مصنوعی.
     * warn به وضعیت Flagged (نیاز به بررسی انسانی) می‌ره، نه Rejected.
     */
    public function toContentStatus(): ContentStatus
    {
        return match ($this) {
            self::Approve => ContentStatus::Approved,
            self::Warn => ContentStatus::Flagged,
            self::Reject => ContentStatus::Rejected,
        };
    }

    /**
     * نتیجه‌ای که برای لایه ۲ تو moderation_logs ثبت می‌شه.
     */
    public function toLogResult(): ModerationResult
    {
        return match ($this) {
            self::Approve => ModerationResult::Passed,
            self::Warn => ModerationResult::Warning,
            self::Reject => ModerationResult::Rejected,
        };
    }
}