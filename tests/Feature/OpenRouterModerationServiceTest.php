<?php

namespace Tests\Feature;

use App\Services\AiModeration\OpenRouterModerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class OpenRouterModerationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_parses_successful_response(): void
    {
        Http::fake([
            '*' => Http::response([
                'model' => 'test-model',
                'choices' => [
                    [
                        'message' => [
                            'content' => json_encode([
                                'flagged' => true,
                                'categories' => ['violence'],
                                'reason' => 'violent content',
                            ]),
                        ],
                    ],
                ],
            ]),
        ]);

        $result = app(OpenRouterModerationService::class)->moderate('sample body');

        $this->assertTrue($result->flagged);
        $this->assertSame(['violence'], $result->categories);
        $this->assertSame('violent content', $result->reason);
    }

    public function test_malformed_json_with_reasoning_inference_flags_violence(): void
    {
        Http::fake([
            '*' => Http::response([
                'model' => 'dots-studio/dots-3-note-preview:free',
                'choices' => [
                    [
                        'finish_reason' => 'length',
                        'message' => [
                            'content' => null,
                            'reasoning' => 'This content explicitly mentions killing a person, so it should be flagged.',
                        ],
                    ],
                ],
            ]),
        ]);

        $result = app(OpenRouterModerationService::class)->moderate('کشتن ادم مهم');

        $this->assertTrue($result->flagged);
        $this->assertContains('violence', $result->categories);
    }

    public function test_extracts_json_from_reasoning_field(): void
    {
        Http::fake([
            '*' => Http::response([
                'choices' => [
                    [
                        'message' => [
                            'content' => null,
                            'reasoning' => 'Done. {"flagged": false, "categories": [], "reason": "ok"}',
                        ],
                    ],
                ],
            ]),
        ]);

        $result = app(OpenRouterModerationService::class)->moderate('hello');

        $this->assertFalse($result->flagged);
    }

    public function test_malformed_json_throws(): void
    {
        Http::fake([
            '*' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'not-json']],
                ],
            ]),
        ]);

        $this->expectException(RuntimeException::class);

        app(OpenRouterModerationService::class)->moderate('sample');
    }

    public function test_provider_http_error_throws_safe_message(): void
    {
        Http::fake([
            '*' => Http::response('rate limited', 429),
        ]);

        try {
            app(OpenRouterModerationService::class)->moderate('sample');
            $this->fail('Expected exception');
        } catch (RuntimeException $e) {
            $this->assertStringNotContainsString('rate limited', $e->getMessage());
        }
    }
}
