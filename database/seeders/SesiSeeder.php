<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sesi;

class SesiSeeder extends Seeder
{
    public function run(): void
    {
        $sesi = [
            [
                'kode' => 'S001',
                'nama' => 'Pagi',
                'jam_mulai' => '07:00',
                'jam_selesai' => '12:00',
                'keterangan' => 'Sesi pagi untuk acara pagi hari'
            ],
            [
                'kode' => 'S002',
                'nama' => 'Siang',
                'jam_mulai' => '12:00',
                'jam_selesai' => '17:00',
                'keterangan' => 'Sesi siang untuk acara siang hari'
            ],
            [
                'kode' => 'S003',
                'nama' => 'Sore',
                'jam_mulai' => '17:00',
                'jam_selesai' => '22:00',
                'keterangan' => 'Sesi sore untuk acara sore hingga malam'
            ],
            [
                'kode' => 'S004',
                'nama' => 'Full Day',
                'jam_mulai' => '07:00',
                'jam_selesai' => '22:00',
                'keterangan' => 'Sesi full day untuk acara seharian penuh'
            ],
            [
                'kode' => 'S005',
                'nama' => 'Malam',
                'jam_mulai' => '18:00',
                'jam_selesai' => '23:00',
                'keterangan' => 'Sesi malam untuk acara malam hari'
            ]
        ];

        foreach ($sesi as $data) {
            Sesi::create($data);
        }
    }
}
