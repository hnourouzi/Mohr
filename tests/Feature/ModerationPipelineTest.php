<?php

namespace Tests\Feature;

use App\Enums\BlacklistSeverity;
use App\Enums\ContentStatus;
use App\Enums\ModerationLayer;
use App\Models\BlacklistWord;
use App\Models\Content;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ModerationPipelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_blocked_content_never_reaches_ai_layer(): void
    {
        BlacklistWord::create(['word' => 'ممنوع', 'severity' => BlacklistSeverity::Block]);

        $user = User::factory()->create();
        $user->wallet->update(['balance' => 5000]);

        $this->actingAs($user)->post(route('panel.contents.store'), [
            'body' => 'این ممنوع است',
            'channel' => 'web',
        ])->assertRedirect();

        $content = Content::first();
        $this->assertSame(ContentStatus::Layer1Rejected, $content->status);

        $this->assertDatabaseMissing('moderation_logs', [
            'content_id' => $content->id,
            'layer' => ModerationLayer::Layer2Ai->value,
        ]);
    }

    public function test_flagged_blacklist_sets_flagged_without_ai(): void
    {
        BlacklistWord::create(['word' => 'مشکوک', 'severity' => BlacklistSeverity::Flag]);

        $user = User::factory()->create();
        $user->wallet->update(['balance' => 5000]);

        $this->actingAs($user)->post(route('panel.contents.store'), [
            'body' => 'متن مشکوک',
            'channel' => 'web',
        ])->assertRedirect();

        $content = Content::first();
        $this->assertSame(ContentStatus::Flagged, $content->status);

        $this->assertDatabaseMissing('moderation_logs', [
            'content_id' => $content->id,
            'layer' => ModerationLayer::Layer2Ai->value,
        ]);
    }

    public function test_retry_from_failed_completes_ai_moderation(): void
    {
        Http::fake([
            '*' => Http::response([
                'choices' => [
                    ['message' => ['content' => json_encode(['flagged' => false, 'categories' => []])]],
                ],
            ]),
        ]);

        $user = User::factory()->create();
        $content = Content::factory()->create([
            'user_id' => $user->id,
            'body' => 'متن سالم',
            'status' => ContentStatus::Failed,
            'retry_count' => 0,
        ]);

        $this->actingAs($user)->post(route('panel.contents.retry', $content))->assertRedirect();

        $this->assertSame(ContentStatus::Approved, $content->fresh()->status);
    }
}
