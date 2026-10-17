<?php

namespace Tests\Feature\Filament;

use App\Actions\RecordSale;
use App\Enums\SaleChannel;
use App\Filament\Resources\Sales\Pages\CreateSale;
use App\Filament\Resources\Sales\Pages\ListSales;
use App\Filament\Resources\Sales\Pages\ViewSale;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\RecipeItem;
use App\Models\Sale;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SaleAdminTest extends TestCase
{
    use RefreshDatabase;

    private InventoryItem $milk;

    private ProductVariant $latte;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());

        $this->milk = InventoryItem::factory()->create(['stock_on_hand' => 1000]);
        $this->latte = ProductVariant::factory()
            ->for(Product::factory()->state(['name' => 'Spanish Latte']))
            ->create(['name' => '16 oz', 'price' => 39]);

        RecipeItem::factory()->for($this->latte)->for($this->milk)->create(['quantity' => 150]);
    }

    public function test_recording_a_sale_takes_its_recipe_out_of_stock(): void
    {
        $undoRepeaterFake = Repeater::fake();

        Livewire::test(CreateSale::class)
            ->fillForm([
                'channel' => SaleChannel::Online->value,
                'reference' => 'RN-RCB4U',
                'customer_name' => 'Joy',
                'lines' => [
                    ['product_variant_id' => $this->latte->id, 'quantity' => 2],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $undoRepeaterFake();

        $sale = Sale::sole();

        $this->assertSame('78.00', $sale->total);
        $this->assertSame(SaleChannel::Online, $sale->channel);
        $this->assertSame('RN-RCB4U', $sale->reference);
        $this->assertSame('700.00', $this->milk->fresh()->stock_on_hand);
    }

    public function test_a_sale_needs_items(): void
    {
        $undoRepeaterFake = Repeater::fake();

        Livewire::test(CreateSale::class)
            ->fillForm(['lines' => []])
            ->call('create')
            ->assertHasFormErrors(['lines']);

        $undoRepeaterFake();

        $this->assertSame(0, Sale::count());
    }

    public function test_voiding_from_the_sale_page_restores_stock(): void
    {
        $sale = app(RecordSale::class)->handle([['product_variant_id' => $this->latte->id, 'quantity' => 2]]);

        Livewire::test(ViewSale::class, ['record' => $sale->getRouteKey()])
            ->callAction('void', ['reason' => 'Customer cancelled'])
            ->assertNotified();

        $this->assertTrue($sale->fresh()->isVoided());
        $this->assertSame('1000.00', $this->milk->fresh()->stock_on_hand);
    }

    public function test_a_voided_sale_cannot_be_voided_again(): void
    {
        $sale = Sale::factory()->voided()->create();

        Livewire::test(ListSales::class)
            ->assertActionHidden(TestAction::make('void')->table($sale));
    }
}
