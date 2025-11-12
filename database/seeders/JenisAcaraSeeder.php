<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisAcaraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisAcara = [
            [
                'kode' => 'ARS',
                'nama' => 'Akad Resepsi',
                'keterangan' => 'Acara akad dan resepsi',
                'harga' => 15500000.00,
                'status_jenis_acara' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'SEM',
                'nama' => 'Seminar',
                'keterangan' => 'Acara seminar dan workshop',
                'harga' => 7500000.00,
                'status_jenis_acara' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'PNG',
                'nama' => 'Pengajian',
                'keterangan' => 'Acara pengajian dan tasyakuran',
                'harga' => 5000000.00,
                'status_jenis_acara' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
           
        ];

        DB::table('jenis_acara')->insert($jenisAcara);
    }
}