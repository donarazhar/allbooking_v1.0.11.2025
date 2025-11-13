<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CabangSeeder::class,
            UserSeeder::class,
            // Tambahkan seeder lain di sini nanti
        ]);

        $this->command->info('');
        $this->command->info('╔════════════════════════════════════════╗');
        $this->command->info('║   ✅ Database Seeding Completed!      ║');
        $this->command->info('╚════════════════════════════════════════╝');
        $this->command->info('');
    }
}