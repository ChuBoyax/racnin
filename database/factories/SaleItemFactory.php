<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SaleItem>
 */
class SaleItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 3);
        $price = fake()->randomElement([29, 39, 49]);

        return [
            'sale_id' => Sale::factory(),
            'product_variant_id' => null,
            'product_name' => ucwords(fake()->words(2, true)),
            'variant_name' => '16 oz',
            'quantity' => $quantity,
            'unit_price' => $price,
            'line_total' => $price * $quantity,
        ];
    }
}
