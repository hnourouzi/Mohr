<?php

namespace Database\Factories;

use App\Enums\ModerationLayer;
use App\Enums\ModerationResult;
use App\Models\Content;
use App\Models\ModerationLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ModerationLog>
 */
class ModerationLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'content_id' => Content::factory(),
            'layer' => ModerationLayer::Layer1Blacklist,
            'result' => ModerationResult::Passed,
            'detail' => null,
            'duration_ms' => fake()->numberBetween(5, 500),
        ];
    }
}
