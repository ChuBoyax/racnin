<?php

namespace Tests\Feature;

use App\Actions\RecordSale;
use App\Actions\VoidSale;
use App\Enums\SaleChannel;
use App\Enums\StockMovementType;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\RecipeItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use LogicException;
use Tests\TestCase;

class SaleTest extends TestCase
{
    use RefreshDatabase;

    private InventoryItem $milk;

    private InventoryItem $cups;

    private ProductVariant $latte;

    protected function setUp(): void
    {
        parent::setUp();

        $this->milk = InventoryItem::factory()->create(['name' => 'Fresh milk', 'stock_on_hand' => 1000]);
        $this->cups = InventoryItem::factory()->create(['name' => 'Cup 16 oz', 'stock_on_hand' => 50]);

        $this->latte = ProductVariant::factory()
            ->for(Product::factory()->state(['name' => 'Spanish Latte']))
            ->create(['name' => '16 oz', 'price' => 39]);

        RecipeItem::factory()->for($this->latte)->for($this->milk)->create(['quantity' => 150]);
        RecipeItem::factory()->for($this->latte)->for($this->cups)->create(['quantity' => 1]);
    }

    public function test_a_sale_takes_each_recipe_ingredient_out_of_stock(): void
    {
        $sale = app(RecordSale::class)->handle([
            ['product_variant_id' => $this->latte->id, 'quantity' => 2],
        ]);

        $this->assertSame('700.00', $this->milk->fresh()->stock_on_hand);
        $this->assertSame('48.00', $this->cups->fresh()->stock_on_hand);
        $this->assertSame(2, $sale->stockMovements()->where('type', StockMovementType::Sale)->count());
    }

    public function test_a_sale_saves_prices_and_names_at_the_time_of_sale(): void
    {
        $sale = app(RecordSale::class)->handle(
            [['product_variant_id' => $this->latte->id, 'quantity' => 3]],
            channel: SaleChannel::Online,
            reference: 'RN-RCB4U',
        );

        $item = $sale->items->sole();

        $this->assertSame('117.00', $sale->fresh()->total);
        $this->assertSame(SaleChannel::Online, $sale->channel);
        $this->assertSame('Spanish Latte (16 oz)', $item->description());
        $this->assertSame('39.00', $item->unit_price);

        $this->latte->update(['price' => 45]);

        $this->assertSame('39.00', $item->fresh()->unit_price);
    }

    public function test_the_same_item_entered_twice_becomes_one_line(): void
    {
        $sale = app(RecordSale::class)->handle([
            ['product_variant_id' => $this->latte->id, 'quantity' => 1],
            ['product_variant_id' => $this->latte->id, 'quantity' => 2],
        ]);

        $this->assertSame(3, $sale->items->sole()->quantity);
        $this->assertSame('550.00', $this->milk->fresh()->stock_on_hand);
    }

    public function test_a_sale_needs_at_least_one_item(): void
    {
        $this->expectException(InvalidArgumentException::class);

        app(RecordSale::class)->handle([['product_variant_id' => $this->latte->id, 'quantity' => 0]]);
    }

    public function test_voiding_a_sale_puts_its_ingredients_back(): void
    {
        $sale = app(RecordSale::class)->handle([['product_variant_id' => $this->latte->id, 'quantity' => 2]]);

        app(VoidSale::class)->handle($sale, 'Customer cancelled');

        $this->assertTrue($sale->isVoided());
        $this->assertSame('Customer cancelled', $sale->void_reason);
        $this->assertSame('1000.00', $this->milk->fresh()->stock_on_hand);
        $this->assertSame('50.00', $this->cups->fresh()->stock_on_hand);
        $this->assertSame(2, $sale->stockMovements()->where('type', StockMovementType::SaleVoid)->count());
    }

    public function test_a_sale_cannot_be_voided_twice(): void
    {
        $sale = app(RecordSale::class)->handle([['product_variant_id' => $this->latte->id, 'quantity' => 1]]);
        app(VoidSale::class)->handle($sale);

        $this->expectException(LogicException::class);

        app(VoidSale::class)->handle($sale);
    }

    public function test_a_variant_reports_its_cost_and_how_many_servings_stock_allows(): void
    {
        $this->milk->forceFill(['unit_cost' => 0.10])->save();
        $this->cups->forceFill(['unit_cost' => 3.50])->save();

        $latte = $this->latte->fresh(['recipeItems.inventoryItem']);

        $this->assertSame(18.5, $latte->costPerServing());
        $this->assertSame(6, $latte->servingsAvailable());
    }
}
