<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Cabang;

class SesiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all cabang
        $cabangList = Cabang::all();

        // Sesi template dengan kode
        $sesiTemplates = [
            [
                'kode' => 'PAGI',
                'nama' => 'Sesi Pagi',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '12:00:00',
            ],
            [
                'kode' => 'SIANG',
                'nama' => 'Sesi Siang',
                'jam_mulai' => '13:00:00',
                'jam_selesai' => '17:00:00',
            ],
            [
                'kode' => 'MALAM',
                'nama' => 'Sesi Malam',
                'jam_mulai' => '18:00:00',
                'jam_selesai' => '22:00:00',
            ],
            [
                'kode' => 'FULLDAY',
                'nama' => 'Sesi Full Day',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '22:00:00',
            ],
        ];

        // Create sesi untuk setiap cabang
        foreach ($cabangList as $index => $cabang) {
            foreach ($sesiTemplates as $sesiIndex => $sesi) {
                // Generate unique kode per cabang
                $kodeUnique = $sesi['kode'] . '_' . strtoupper(substr($cabang->nama, 0, 3)) . '_' . ($index + 1);

                DB::table('sesi')->insert([
                    'cabang_id' => $cabang->id,
                    'kode' => $kodeUnique,
                    'nama' => $sesi['nama'],
                    'jam_mulai' => $sesi['jam_mulai'],
                    'jam_selesai' => $sesi['jam_selesai'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->command->info("✅ Sesi created for: {$cabang->nama}");
        }

        $this->command->info('🎉 Sesi seeding completed for all cabang!');
    }
}
