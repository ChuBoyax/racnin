<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tests run outside local development, like a live deployment does.
 */
class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_live_site_gets_the_menu_and_stock_but_no_default_login_or_demo_sales(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(count(config('racnin.items')), Product::count());
        $this->assertGreaterThan(0, InventoryItem::count());
        $this->assertSame(0, User::count());
        $this->assertSame(0, Sale::count());
    }

    public function test_a_live_site_gets_the_admin_from_the_environment(): void
    {
        config(['racnin.admin' => ['email' => 'owner@racnin.example', 'password' => 'a-long-secret-password']]);

        $this->seed(DatabaseSeeder::class);

        $admin = User::sole();

        $this->assertSame('owner@racnin.example', $admin->email);
        $this->assertTrue(Hash::check('a-long-secret-password', $admin->password));
    }
}
