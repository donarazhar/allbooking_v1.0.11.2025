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
        // Urutan seeder penting! Role harus pertama
        $this->call([
            // 1. Master data yang tidak bergantung
            RoleSeeder::class,          // Harus pertama (diperlukan oleh UserSeeder)
            SesiSeeder::class,           // Master sesi waktu
            JenisAcaraSeeder::class,     // Master jenis acara
            CateringSeeder::class,       // Master catering
            
            // 2. User data (bergantung pada Role)
            UserSeeder::class,           // User dengan role 'user'
            
            // 3. Data transaksi (jika ada)
            // BukaJadwalSeeder::class,  // Uncomment jika sudah dibuat
            // BookingSeeder::class,      // Uncomment jika sudah dibuat
        ]);
    }
}
