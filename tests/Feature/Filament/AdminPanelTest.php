<?php

namespace Tests\Feature\Filament;

use App\Filament\Widgets\BestSellersChart;
use App\Filament\Widgets\LowStockItems;
use App\Filament\Widgets\SalesChart;
use App\Filament\Widgets\ShopOverview;
use App\Models\InventoryItem;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_the_login_page_loads(): void
    {
        $this->get(route('filament.admin.auth.login'))
            ->assertOk()
            ->assertSee('Sign in');
    }

    public function test_signed_in_staff_skip_the_login_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('filament.admin.auth.login'))
            ->assertRedirect('/admin');
    }

    /**
     * @return array<string, array{string}>
     */
    public static function pages(): array
    {
        return [
            'dashboard' => ['/admin'],
            'sales' => ['/admin/sales'],
            'record a sale' => ['/admin/sales/create'],
            'stock items' => ['/admin/inventory-items'],
            'new stock item' => ['/admin/inventory-items/create'],
            'stock history' => ['/admin/stock-movements'],
            'suppliers' => ['/admin/suppliers'],
            'products' => ['/admin/products'],
            'new product' => ['/admin/products/create'],
        ];
    }

    #[DataProvider('pages')]
    public function test_staff_can_open_every_page(string $url): void
    {
        $this->actingAs(User::factory()->create())
            ->get($url)
            ->assertOk();
    }

    public function test_sales_cannot_be_edited(): void
    {
        $sale = Sale::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get("/admin/sales/{$sale->id}/edit")
            ->assertNotFound();
    }

    public function test_the_dashboard_widgets_render(): void
    {
        $this->actingAs(User::factory()->create());

        Sale::factory()->count(3)->create();
        $milk = InventoryItem::factory()->lowStock()->create(['name' => 'Fresh milk']);

        Livewire::test(ShopOverview::class)->assertOk()->assertSee('Low on stock');
        Livewire::test(SalesChart::class)->assertOk();
        Livewire::test(BestSellersChart::class)->assertOk();
        Livewire::test(LowStockItems::class)->assertOk()->assertCanSeeTableRecords([$milk]);
    }
}
