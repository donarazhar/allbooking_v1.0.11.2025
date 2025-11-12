<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SesiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sesi = [
            [
                'kode' => 'SP',
                'nama' => 'Sesi Pagi',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '12:00:00',
                'keterangan' => 'Sesi booking untuk waktu pagi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'SM',
                'nama' => 'Sesi Malam',
                'jam_mulai' => '15:00:00',
                'jam_selesai' => '21:00:00',
                'keterangan' => 'Sesi booking untuk waktu malam',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'FD',
                'nama' => 'Full Days',
                'jam_mulai' => '07:00:00',
                'jam_selesai' => '13:00:00',
                'keterangan' => 'Sesi booking untuk acara seminar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('sesi')->insert($sesi);
    }
}