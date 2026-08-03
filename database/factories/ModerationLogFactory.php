<?php

namespace Database\Factories;

use App\Models\ModerationLog;
use App\Models\Content;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ModerationLog>
 */
class ModerationLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content_id' => Content::inRandomOrder()->first()->id,
            'layer' => $this->faker->randomElement(['content', 'comment']),
            'result' => $this->faker->randomElement(['block', 'flag']),
            'detail' => $this->faker->randomElements(['detail1', 'detail2', 'detail3'], 3),
            'duration_ms' => $this->faker->numberBetween(1000, 10000),
        ];
    }
}
