<?php

namespace Database\Factories;

use App\Enums\SaleChannel;
use App\Models\Sale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sale>
 */
class SaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => null,
            'channel' => fake()->randomElement(SaleChannel::cases()),
            'reference' => null,
            'customer_name' => fake()->optional()->firstName(),
            'total' => fake()->randomElement([39, 78, 104, 147]),
            'notes' => null,
            'sold_at' => now(),
        ];
    }

    public function voided(): static
    {
        return $this->state(fn (array $attributes): array => [
            'voided_at' => now(),
            'void_reason' => 'Entered by mistake',
        ]);
    }
}
