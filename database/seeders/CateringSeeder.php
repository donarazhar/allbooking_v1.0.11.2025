<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CateringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $caterings = [
            [
                'nama' => 'Catering Nusantara',
                'email' => 'nusantara@catering.com',
                'no_hp' => '021-5551234',
                'alamat' => 'Jl. Melawai Raya No. 100, Jakarta Selatan, DKI Jakarta',
                'password' => Hash::make('password'),
                'foto' => null,
                'keterangan' => 'Spesialis masakan Nusantara untuk acara pernikahan, seminar, dan konferensi. Berpengalaman lebih dari 10 tahun.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Boga Catering',
                'email' => 'boga@catering.com',
                'no_hp' => '021-5551235',
                'alamat' => 'Jl. Senopati No. 45, Jakarta Selatan, DKI Jakarta',
                'password' => Hash::make('password'),
                'foto' => null,
                'keterangan' => 'Catering premium untuk berbagai acara. Menu variatif dengan harga terjangkau.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Royal Catering',
                'email' => 'royal@catering.com',
                'no_hp' => '021-5551236',
                'alamat' => 'Jl. Kemang Raya No. 28, Jakarta Selatan, DKI Jakarta',
                'password' => Hash::make('password'),
                'foto' => null,
                'keterangan' => 'Catering mewah untuk acara eksklusif. Spesialis menu internasional dan fusion.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Sari Rasa Catering',
                'email' => 'sarirasa@catering.com',
                'no_hp' => '021-5551237',
                'alamat' => 'Jl. Panglima Polim No. 12, Jakarta Selatan, DKI Jakarta',
                'password' => Hash::make('password'),
                'foto' => null,
                'keterangan' => 'Catering halal dengan menu prasmanan dan buffet. Cocok untuk acara kantor dan keluarga.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Dapur Mama Catering',
                'email' => 'dapurmama@catering.com',
                'no_hp' => '021-5551238',
                'alamat' => 'Jl. Fatmawati No. 55, Jakarta Selatan, DKI Jakarta',
                'password' => Hash::make('password'),
                'foto' => null,
                'keterangan' => 'Catering rumahan dengan cita rasa homemade. Harga ekonomis untuk berbagai acara.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('catering')->insert($caterings);
    }
}