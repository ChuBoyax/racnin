<?php

namespace Tests\Feature;

use App\Actions\RecordStockCount;
use App\Actions\RecordStockMovement;
use App\Enums\StockMovementType;
use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_purchase_adds_stock_and_logs_the_running_balance(): void
    {
        $user = User::factory()->create();
        $milk = InventoryItem::factory()->create(['stock_on_hand' => 1000, 'unit_cost' => 0.10]);

        $movement = app(RecordStockMovement::class)->handle($milk, StockMovementType::Purchase, 4000, unitCost: 0.10, reference: 'INV-1001', user: $user);

        $this->assertSame('5000.00', $milk->fresh()->stock_on_hand);
        $this->assertSame('5000.00', $movement->balance_after);
        $this->assertSame('INV-1001', $movement->reference);
        $this->assertTrue($movement->user->is($user));
    }

    public function test_a_purchase_blends_the_new_cost_into_a_moving_average(): void
    {
        $beans = InventoryItem::factory()->create(['stock_on_hand' => 1000, 'unit_cost' => 0.80]);

        app(RecordStockMovement::class)->handle($beans, StockMovementType::Purchase, 1000, unitCost: 1.00);

        $this->assertSame('0.9000', $beans->fresh()->unit_cost);
    }

    public function test_a_purchase_into_empty_stock_takes_the_new_cost(): void
    {
        $beans = InventoryItem::factory()->outOfStock()->create(['unit_cost' => 0.80]);

        app(RecordStockMovement::class)->handle($beans, StockMovementType::Purchase, 500, unitCost: 1.20);

        $this->assertSame('1.2000', $beans->fresh()->unit_cost);
    }

    public function test_waste_takes_stock_out_and_keeps_the_cost(): void
    {
        $cups = InventoryItem::factory()->create(['stock_on_hand' => 100, 'unit_cost' => 3.50]);

        $movement = app(RecordStockMovement::class)->handle($cups, StockMovementType::Waste, -12, notes: 'Cracked');

        $this->assertSame('88.00', $cups->fresh()->stock_on_hand);
        $this->assertSame('-12.00', $movement->quantity);
        $this->assertSame('3.5000', $cups->fresh()->unit_cost);
    }

    public function test_the_item_passed_in_reflects_the_new_balance(): void
    {
        $cups = InventoryItem::factory()->create(['stock_on_hand' => 100]);

        app(RecordStockMovement::class)->handle($cups, StockMovementType::Waste, -10);

        $this->assertSame('90.00', $cups->stock_on_hand);
    }

    public function test_a_zero_quantity_is_rejected(): void
    {
        $cups = InventoryItem::factory()->create();

        $this->expectException(InvalidArgumentException::class);

        app(RecordStockMovement::class)->handle($cups, StockMovementType::Waste, 0);
    }

    public function test_a_count_records_the_difference_as_an_adjustment(): void
    {
        $sugar = InventoryItem::factory()->create(['stock_on_hand' => 1000]);

        $movement = app(RecordStockCount::class)->handle($sugar, 940);

        $this->assertSame(StockMovementType::Adjustment, $movement->type);
        $this->assertSame('-60.00', $movement->quantity);
        $this->assertSame('940.00', $sugar->fresh()->stock_on_hand);
    }

    public function test_a_count_that_matches_records_nothing(): void
    {
        $sugar = InventoryItem::factory()->create(['stock_on_hand' => 1000]);

        $this->assertNull(app(RecordStockCount::class)->handle($sugar, 1000));
        $this->assertSame(0, $sugar->stockMovements()->count());
    }

    public function test_low_stock_includes_items_at_or_under_their_reorder_level(): void
    {
        $plenty = InventoryItem::factory()->create(['stock_on_hand' => 500, 'reorder_level' => 100]);
        $atLevel = InventoryItem::factory()->create(['stock_on_hand' => 100, 'reorder_level' => 100]);
        $out = InventoryItem::factory()->outOfStock()->create();

        $lowStock = InventoryItem::lowStock()->pluck('id');

        $this->assertFalse($lowStock->contains($plenty->id));
        $this->assertTrue($lowStock->contains($atLevel->id));
        $this->assertTrue($lowStock->contains($out->id));
        $this->assertTrue($out->isOutOfStock());
    }
}
