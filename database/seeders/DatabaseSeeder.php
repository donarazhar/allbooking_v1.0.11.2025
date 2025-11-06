<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Jalankan seeder dalam urutan yang benar
        // Roles harus pertama karena users membutuhkan role_id
        $this->call([
            RoleSeeder::class,      // 1. Roles (3 records)
            SesiSeeder::class,      // 2. Sesi (4 records)
            JenisAcaraSeeder::class, // 3. Jenis Acara (5 records)
            CateringSeeder::class,  // 4. Catering (5 records)
            UserSeeder::class,      // 5. Users (5 records: 1 Admin, 1 Pimpinan, 3 Klien)
        ]);
    }
}