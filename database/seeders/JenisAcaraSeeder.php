<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Cabang;

class JenisAcaraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all cabang
        $cabangList = Cabang::all();

        // Jenis Acara templates dengan kode dan variasi harga
        $jenisAcaraTemplates = [
            [
                'kode' => 'NIKAH',
                'nama' => 'Pernikahan',
                'base_harga' => 15000000,
            ],
            [
                'kode' => 'SEMINAR',
                'nama' => 'Seminar',
                'base_harga' => 5000000,
            ],
            [
                'kode' => 'WORKSHOP',
                'nama' => 'Workshop',
                'base_harga' => 4000000,
            ],
            [
                'kode' => 'RAPAT',
                'nama' => 'Rapat Perusahaan',
                'base_harga' => 3000000,
            ],
            [
                'kode' => 'ULTAH',
                'nama' => 'Ulang Tahun',
                'base_harga' => 8000000,
            ],
            [
                'kode' => 'ARISAN',
                'nama' => 'Arisan',
                'base_harga' => 2500000,
            ],
            [
                'kode' => 'PELATIHAN',
                'nama' => 'Pelatihan',
                'base_harga' => 4500000,
            ],
            [
                'kode' => 'GATHERING',
                'nama' => 'Gathering',
                'base_harga' => 6000000,
            ],
        ];

        // Create jenis acara untuk setiap cabang dengan variasi harga
        foreach ($cabangList as $index => $cabang) {
            foreach ($jenisAcaraTemplates as $jenisAcara) {
                // Variasi harga berdasarkan index cabang (untuk simulasi perbedaan harga antar cabang)
                $variasiHarga = ($index + 1) * 500000; // Cabang 1: +500k, Cabang 2: +1jt, dst
                $hargaFinal = $jenisAcara['base_harga'] + $variasiHarga;

                // Generate unique kode per cabang
                $kodeUnique = $jenisAcara['kode'] . '_' . strtoupper(substr($cabang->nama, 0, 3)) . '_' . ($index + 1);

                DB::table('jenis_acara')->insert([
                    'cabang_id' => $cabang->id,
                    'kode' => $kodeUnique,
                    'nama' => $jenisAcara['nama'],
                    'harga' => $hargaFinal,
                    'status_jenis_acara' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->command->info("✅ Jenis Acara created for: {$cabang->nama}");
        }

        $this->command->info('🎉 Jenis Acara seeding completed for all cabang!');
    }
}
