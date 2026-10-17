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
        $this->seedAdmin();

        $this->call([
            InventorySeeder::class,
            MenuSeeder::class,
        ]);

        // Sample trading history, so the dashboard isn't empty while you try things out.
        if (app()->isLocal()) {
            $this->call(DemoSalesSeeder::class);
        }
    }

    /**
     * Local development gets a known login. Anywhere else the account comes
     * from ADMIN_EMAIL and ADMIN_PASSWORD, so no default password goes live.
     */
    private function seedAdmin(): void
    {
        if (app()->isLocal()) {
            User::firstOrCreate(
                ['email' => 'admin@racnin.test'],
                ['name' => 'RacNin Admin', 'password' => 'password', 'email_verified_at' => now()],
            );

            return;
        }

        ['email' => $email, 'password' => $password] = config('racnin.admin');

        if (blank($email) || blank($password)) {
            $this->command?->warn('No admin created. Set ADMIN_EMAIL and ADMIN_PASSWORD, or run: php artisan make:filament-user');

            return;
        }

        User::firstOrCreate(
            ['email' => $email],
            ['name' => 'RacNin Admin', 'password' => $password, 'email_verified_at' => now()],
        );
    }
}
