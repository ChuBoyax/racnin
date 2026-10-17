<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use App\Models\ProductVariant;
use App\Models\RecipeItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RecipeItem>
 */
class RecipeItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_variant_id' => ProductVariant::factory(),
            'inventory_item_id' => InventoryItem::factory(),
            'quantity' => fake()->randomElement([1, 18, 30, 150]),
        ];
    }
}
