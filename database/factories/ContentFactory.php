<?php

namespace Database\Factories;

use App\Models\Content;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Content>
 */
class ContentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'body' => $this->faker->text(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'rejection_reason' => $this->faker->sentence(),
            'ai_categories' => $this->faker->randomElements(['category1', 'category2', 'category3'], 3),
            'ai_raw_response' => $this->faker->randomElements(['response1', 'response2', 'response3'], 3),
            'retry_count' => $this->faker->numberBetween(0, 3),
            'processed_at' => $this->faker->dateTime(),
        ];
    }
}
