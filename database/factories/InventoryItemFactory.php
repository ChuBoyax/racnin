<?php

namespace Database\Factories;

use App\Enums\InventoryCategory;
use App\Enums\StockUnit;
use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryItem>
 */
class InventoryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => null,
            'name' => ucfirst(fake()->unique()->words(2, true)),
            'sku' => strtoupper(fake()->unique()->bothify('ITEM-####')),
            'category' => fake()->randomElement(InventoryCategory::cases()),
            'unit' => fake()->randomElement(StockUnit::cases()),
            'stock_on_hand' => 1000,
            'reorder_level' => 200,
            'unit_cost' => fake()->randomFloat(2, 0.1, 5),
            'is_active' => true,
            'notes' => null,
        ];
    }

    /**
     * At or under its reorder level, but not out.
     */
    public function lowStock(): static
    {
        return $this->state(fn (array $attributes): array => [
            'stock_on_hand' => 50,
            'reorder_level' => 200,
        ]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes): array => [
            'stock_on_hand' => 0,
        ]);
    }
}
