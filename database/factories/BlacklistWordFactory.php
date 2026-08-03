<?php

namespace Database\Factories;

use App\Models\BlacklistWord;
use App\Enums\BlacklistSeverity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BlacklistWord>
 */
class BlacklistWordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'word' => $this->faker->word(),
            'severity' => $this->faker->randomElement(BlacklistSeverity::cases())->value,
        ];
    }
}
