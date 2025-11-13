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
            // Master Data (sudah ada sebelumnya)
            RoleSeeder::class,
            CabangSeeder::class,
            UserSeeder::class,
            CateringSeeder::class,

            // NEW: Seeder untuk testing
            SesiSeeder::class,           // ← Tambahkan
            JenisAcaraSeeder::class,     // ← Tambahkan
            BukaJadwalSeeder::class,     // ← Tambahkan
        ]);
    }
}
