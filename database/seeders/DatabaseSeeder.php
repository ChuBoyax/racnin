<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@racnin.test'],
            ['name' => 'RacNin Admin', 'password' => 'password', 'email_verified_at' => now()],
        );

        $this->call([
            InventorySeeder::class,
            MenuSeeder::class,
        ]);

        // Sample trading history, so the dashboard isn't empty while you try things out.
        if (app()->isLocal()) {
            $this->call(DemoSalesSeeder::class);
        }
    }
}
