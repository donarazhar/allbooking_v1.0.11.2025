<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Catering;

class CateringSeeder extends Seeder
{
    public function run(): void
    {
        $catering = [
            [
                'nama' => 'Boga Rasa Catering',
                'no_hp' => '081234567890',
                'email' => 'a@catering.com',
                'alamat' => 'Jl. Merdeka No. 45, Jakarta Pusat',
                'keterangan' => 'Spesialis masakan Nusantara dan internasional'
            ],
            [
                'nama' => 'Santapan Nikmat',
                'no_hp' => '081234567891',
                'email' => 'b@catering.com',
                'alamat' => 'Jl. Sudirman No. 123, Jakarta Selatan',
                'keterangan' => 'Catering untuk acara pernikahan dan gathering'
            ],
            [
                'nama' => 'Dapur Mama',
                'no_hp' => '081234567892',
                'email' => 'c@catering.com',
                'alamat' => 'Jl. Gatot Subroto No. 78, Jakarta Barat',
                'keterangan' => 'Home cooking dengan cita rasa istimewa'
            ],
            [
                'nama' => 'Prasmanan Deluxe',
                'no_hp' => '081234567893',
                'email' => 'd@catering.com',
                'alamat' => 'Jl. Thamrin No. 56, Jakarta Pusat',
                'keterangan' => 'Prasmanan mewah untuk acara besar'
            ],
            [
                'nama' => 'Cita Rasa Jaya',
                'no_hp' => '081234567894',
                'email' => 'e@catering.com',
                'alamat' => 'Jl. Kebon Jeruk No. 90, Jakarta Barat',
                'keterangan' => 'Catering halal dengan menu variatif'
            ],
            [
                'nama' => 'Nasi Box Express',
                'no_hp' => '081234567895',
                'email' => 'f@catering.com',
                'alamat' => 'Jl. Raya Pasar Minggu No. 34, Jakarta Selatan',
                'keterangan' => 'Spesialis nasi box untuk meeting dan seminar'
            ],
            [
                'nama' => 'Royal Catering Service',
                'no_hp' => '081234567896',
                'email' => 'g@catering.com',
                'alamat' => 'Jl. HR Rasuna Said No. 100, Jakarta Selatan',
                'keterangan' => 'Catering premium dengan service terbaik'
            ],
            [
                'nama' => 'Sari Rasa Catering',
                'no_hp' => '081234567897',
                'email' => 'h@catering.com',
                'alamat' => 'Jl. Cikini Raya No. 25, Jakarta Pusat',
                'keterangan' => 'Menu tradisional dan modern'
            ],
            [
                'nama' => 'Golden Feast',
                'no_hp' => '081234567898',
                'email' => 'i@catering.com',
                'alamat' => 'Jl. Pejaten Raya No. 67, Jakarta Selatan',
                'keterangan' => 'Catering untuk event corporate'
            ],
            [
                'nama' => 'Sederhana Catering',
                'no_hp' => '081234567899',
                'email' => 'j@catering.com',
                'alamat' => 'Jl. Tebet Timur No. 12, Jakarta Selatan',
                'keterangan' => 'Catering ekonomis dengan rasa premium'
            ]
        ];

        foreach ($catering as $data) {
            Catering::create($data);
        }
    }
}
