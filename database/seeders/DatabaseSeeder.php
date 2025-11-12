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
            RoleSeeder::class,      
            SesiSeeder::class,      
            JenisAcaraSeeder::class, 
            CateringSeeder::class,  
            UserSeeder::class,   
            BukaJadwalSeeder::class,
            BookingSeeder::class,

        ]);
    }
}