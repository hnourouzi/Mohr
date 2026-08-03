<?php

namespace Database\Factories;

use App\Models\WalletTransaction;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\WalletTransactionType;

/**
 * @extends Factory<WalletTransaction>
 */
class WalletTransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'wallet_id'     => Wallet::inRandomOrder()->first()->id,
            'amount'        => $this->faker->numberBetween(1000, 100000),
            'type'          => $this->faker->randomElement(WalletTransactionType::cases())->value,
            'balance_after' => $this->faker->numberBetween(1000, 100000),
            'reference_type' => $this->faker->randomElement(['wallet', 'content']),
            'reference_id'   => $this->faker->numberBetween(1, 100000),
            'description'   => $this->faker->sentence(),
        ];
    }
}
