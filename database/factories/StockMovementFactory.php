<?php

namespace Database\Factories;

use App\Enums\StockMovementType;
use App\Models\InventoryItem;
use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockMovement>
 */
class StockMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'inventory_item_id' => InventoryItem::factory(),
            'sale_id' => null,
            'user_id' => null,
            'type' => StockMovementType::Purchase,
            'quantity' => 500,
            'balance_after' => 1500,
            'unit_cost' => 1,
            'reference' => null,
            'notes' => null,
            'occurred_at' => now(),
        ];
    }
}
