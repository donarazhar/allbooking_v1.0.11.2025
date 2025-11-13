<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cabang;
use Illuminate\Support\Facades\DB;

class CabangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table untuk development
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Cabang::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $cabangList = [
            [
                'kode' => 'AGUNG',
                'nama' => 'Aula Masjid Agung Al Azhar',
                'alamat' => 'Jl. Sisingamangaraja, Kebayoran Baru',
                'kota' => 'Jakarta Selatan',
                'no_telp' => '021-7395663',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'BINTARO',
                'nama' => 'Aula Masjid Bintaro Al Azhar',
                'alamat' => 'Jl. Bintaro Utama 3A, Sektor 3A',
                'kota' => 'Tangerang Selatan',
                'no_telp' => '021-7355088',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'CIKARANG',
                'nama' => 'Aula Masjid Cikarang Al Azhar',
                'alamat' => 'Jl. Fatmawati Raya, Lippo Cikarang',
                'kota' => 'Bekasi',
                'no_telp' => '021-89841234',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'SENTRA',
                'nama' => 'Aula Masjid Sentra Al Azhar',
                'alamat' => 'Jl. Kemang Timur Raya No. 1',
                'kota' => 'Jakarta Selatan',
                'no_telp' => '021-7195959',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($cabangList as $cabang) {
            Cabang::create($cabang);
        }

        $this->command->info('✅ Cabang seeded successfully!');
        $this->command->table(
            ['Kode', 'Nama', 'Kota', 'No Telp'],
            collect($cabangList)->map(function ($cabang) {
                return [
                    $cabang['kode'],
                    $cabang['nama'],
                    $cabang['kota'],
                    $cabang['no_telp']
                ];
            })
        );
    }
}