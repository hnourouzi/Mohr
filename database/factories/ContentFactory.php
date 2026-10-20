<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Models\Content;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Content>
 */
class ContentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'body' => fake()->paragraph(),
            'channel' => 'web',
            'status' => ContentStatus::Pending,
            'cost' => config('moderation.cost_per_request', 1000),
            'rejection_reason' => null,
            'ai_categories' => null,
            'ai_raw_response' => null,
            'retry_count' => 0,
            'processed_at' => null,
        ];
    }
}
