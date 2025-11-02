<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisAcara;

class JenisAcaraSeeder extends Seeder
{
    public function run(): void
    {
        $jenisAcara = [
            [
                'kode' => 'JA001',
                'nama' => 'Pernikahan',
                'keterangan' => 'Acara pernikahan atau resepsi',
                'harga' => '1000000'
            ],
            [
                'kode' => 'JA002',
                'nama' => 'Seminar',
                'keterangan' => 'Seminar, workshop, atau pelatihan',
                'harga' => '1000000'
            ],
            [
                'kode' => 'JA003',
                'nama' => 'Rapat',
                'keterangan' => 'Rapat perusahaan atau organisasi',
                'harga' => '1000000'
            ],
            [
                'kode' => 'JA004',
                'nama' => 'Ulang Tahun',
                'keterangan' => 'Pesta ulang tahun atau perayaan',
                'harga' => '1000000'
            ],
            [
                'kode' => 'JA005',
                'nama' => 'Gathering',
                'keterangan' => 'Family gathering atau reuni',
                'harga' => '1000000'
            ],
            [
                'kode' => 'JA006',
                'nama' => 'Konferensi',
                'keterangan' => 'Konferensi atau forum diskusi',
                'harga' => '1000000'
            ],
            [
                'kode' => 'JA007',
                'nama' => 'Pameran',
                'keterangan' => 'Pameran produk atau karya',
                'harga' => '1000000'
            ],
            [
                'kode' => 'JA008',
                'nama' => 'Wisuda',
                'keterangan' => 'Acara wisuda atau kelulusan',
                'harga' => '1000000'
            ],
            [
                'kode' => 'JA009',
                'nama' => 'Pertunjukan',
                'keterangan' => 'Pertunjukan seni atau hiburan',
                'harga' => '1000000'
            ],
            [
                'kode' => 'JA010',
                'nama' => 'Syukuran',
                'keterangan' => 'Acara syukuran atau doa bersama',
                'harga' => '1000000'
            ],
            [
                'kode' => 'JA011',
                'nama' => 'Launching',
                'keterangan' => 'Launching produk atau layanan baru',
                'harga' => '1000000'
            ],
            [
                'kode' => 'JA012',
                'nama' => 'Meeting',
                'keterangan' => 'Meeting internal atau eksternal',
                'harga' => '1000000'
            ]
        ];

        foreach ($jenisAcara as $data) {
            JenisAcara::create($data);
        }
    }
}
