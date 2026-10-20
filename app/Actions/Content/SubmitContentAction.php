<?php

namespace App\Actions\Content;

use App\Actions\Wallet\DeductCreditAction;
use App\Enums\ContentStatus;
use App\Jobs\ModerateContentJob;
use App\Models\Content;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SubmitContentAction
{
    public function __construct(
        private readonly DeductCreditAction $deductCreditAction,
    ) {
    }

    public function execute(User $user, string $body, ?string $channel = null): Content
    {
        return DB::transaction(function () use ($user, $body, $channel) {
            $cost = (int) config('moderation.cost_per_request', 1000);

            $content = Content::create([
                'user_id' => $user->id,
                'body' => $body,
                'channel' => $channel,
                'status' => ContentStatus::Pending,
                'cost' => $cost,
            ]);

            $this->deductCreditAction->execute($user, $content);

            ModerateContentJob::dispatch($content)
                ->onQueue('moderation')
                ->afterCommit();

            return $content;
        });
    }
}
