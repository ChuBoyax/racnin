<?php

namespace Tests\Feature\Filament;

use App\Enums\InventoryCategory;
use App\Enums\StockMovementType;
use App\Enums\StockUnit;
use App\Filament\Resources\InventoryItems\Pages\CreateInventoryItem;
use App\Filament\Resources\InventoryItems\Pages\EditInventoryItem;
use App\Filament\Resources\InventoryItems\Pages\ListInventoryItems;
use App\Models\InventoryItem;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InventoryAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs($this->user = User::factory()->create());
    }

    public function test_creating_an_item_records_its_opening_stock_in_the_ledger(): void
    {
        Livewire::test(CreateInventoryItem::class)
            ->fillForm([
                'name' => 'Fresh milk',
                'sku' => 'milk-fresh',
                'category' => InventoryCategory::Dairy->value,
                'unit' => StockUnit::Milliliter->value,
                'opening_stock' => 12000,
                'reorder_level' => 5000,
                'unit_cost' => 0.085,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $milk = InventoryItem::sole();
        $movement = $milk->stockMovements()->sole();

        $this->assertSame('MILK-FRESH', $milk->sku);
        $this->assertSame('12000.00', $milk->stock_on_hand);
        $this->assertSame(StockMovementType::Adjustment, $movement->type);
        $this->assertSame('Opening stock', $movement->notes);
        $this->assertTrue($movement->user->is($this->user));
    }

    public function test_stock_in_from_the_table_adds_stock_at_the_price_paid(): void
    {
        $beans = InventoryItem::factory()->create(['stock_on_hand' => 1000, 'unit_cost' => 0.50]);

        Livewire::test(ListInventoryItems::class)
            ->callAction(TestAction::make('stockIn')->table($beans), [
                'quantity' => 1000,
                'total_cost' => 600,
                'reference' => 'DR-1017',
            ])
            ->assertHasNoFormErrors()
            ->assertNotified();

        $beans->refresh();

        $this->assertSame('2000.00', $beans->stock_on_hand);
        $this->assertSame('0.5500', $beans->unit_cost);
        $this->assertSame('DR-1017', $beans->stockMovements()->sole()->reference);
    }

    public function test_waste_needs_a_reason(): void
    {
        $cups = InventoryItem::factory()->create(['stock_on_hand' => 100]);

        Livewire::test(ListInventoryItems::class)
            ->callAction(TestAction::make('recordWaste')->table($cups), ['quantity' => 5])
            ->assertHasFormErrors(['reason' => 'required']);

        $this->assertSame('100.00', $cups->fresh()->stock_on_hand);
    }

    public function test_a_stock_count_from_the_edit_page_corrects_the_balance(): void
    {
        $sugar = InventoryItem::factory()->create(['stock_on_hand' => 1000]);

        Livewire::test(EditInventoryItem::class, ['record' => $sugar->getRouteKey()])
            ->callAction('countStock', ['counted' => 950, 'notes' => 'Weekly count'])
            ->assertHasNoFormErrors();

        $this->assertSame('950.00', $sugar->fresh()->stock_on_hand);
        $this->assertSame('-50.00', $sugar->stockMovements()->sole()->quantity);
    }

    public function test_the_low_stock_tab_only_lists_items_at_or_under_their_reorder_level(): void
    {
        $low = InventoryItem::factory()->lowStock()->create();
        $plenty = InventoryItem::factory()->create(['stock_on_hand' => 900, 'reorder_level' => 100]);

        Livewire::withQueryParams(['tab' => 'low-stock'])
            ->test(ListInventoryItems::class)
            ->assertCanSeeTableRecords([$low])
            ->assertCanNotSeeTableRecords([$plenty]);
    }

    public function test_the_unit_is_locked_once_stock_has_moved(): void
    {
        $milk = InventoryItem::factory()->create(['unit' => StockUnit::Milliliter]);
        $milk->stockMovements()->create(['type' => StockMovementType::Purchase, 'quantity' => 10, 'balance_after' => 10, 'occurred_at' => now()]);

        Livewire::test(EditInventoryItem::class, ['record' => $milk->getRouteKey()])
            ->assertFormFieldDisabled('unit');
    }
}
