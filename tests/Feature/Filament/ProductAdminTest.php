<?php

namespace Tests\Feature\Filament;

use App\Enums\MenuCategory;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\RecipeItem;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_a_product_is_saved_with_its_sizes_and_recipes(): void
    {
        $milk = InventoryItem::factory()->create(['name' => 'Fresh milk']);
        $cup = InventoryItem::factory()->create(['name' => 'Cup 16 oz']);

        $undoRepeaterFake = Repeater::fake();

        Livewire::test(CreateProduct::class)
            ->fillForm([
                'name' => 'Ube Latte',
                'slug' => 'ube-latte',
                'category' => MenuCategory::NonCoffee->value,
                'variants' => [
                    [
                        'name' => '16 oz',
                        'price' => 39,
                        'recipeItems' => [
                            ['inventory_item_id' => $milk->id, 'quantity' => 200],
                            ['inventory_item_id' => $cup->id, 'quantity' => 1],
                        ],
                    ],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $undoRepeaterFake();

        $variant = Product::sole()->variants->sole();

        $this->assertSame('16 oz', $variant->name);
        $this->assertSame('39.00', $variant->price);
        $this->assertEqualsCanonicalizing([$milk->id, $cup->id], $variant->recipeItems->pluck('inventory_item_id')->all());
    }

    public function test_the_list_shows_how_many_servings_stock_can_make(): void
    {
        $milk = InventoryItem::factory()->create(['stock_on_hand' => 1000]);
        $latte = ProductVariant::factory()->for(Product::factory()->state(['name' => 'Spanish Latte']))->create();
        RecipeItem::factory()->for($latte)->for($milk)->create(['quantity' => 150]);

        Livewire::test(ListProducts::class)
            ->assertCanSeeTableRecords([$latte->product])
            ->assertTableColumnStateSet('servings', 6, $latte->product);
    }
}
