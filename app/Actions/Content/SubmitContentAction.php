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
            $content = Content::create([
                'user_id' => $user->id,
                'body' => $body,
                'channel' => $channel,
                'status' => ContentStatus::Pending,
                'cost' => config('moderation.cost_per_request', 1000),
            ]);

            $this->deductCreditAction->execute($user, $content);

            ModerateContentJob::dispatch($content)->onQueue('moderation');
            return $content;
        });
    }
}