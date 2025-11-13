<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table untuk development
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $roles = [
            [
                'kode' => 'SUPERADMIN',
                'nama' => 'Super Administrator',
                'keterangan' => 'Memiliki akses penuh ke seluruh sistem dan semua cabang. Dapat mengelola master data, users, dan monitoring seluruh transaksi.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'ADMIN',
                'nama' => 'Admin Cabang',
                'keterangan' => 'Mengelola operasional cabang masing-masing. Dapat mengelola sesi, jenis acara, jadwal, booking, dan pembayaran untuk cabang yang ditugaskan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'PIMPINAN',
                'nama' => 'Pimpinan Cabang',
                'keterangan' => 'Monitoring dan approval untuk cabang yang dipimpin. Dapat melihat laporan, monitoring booking, dan memberikan approval untuk transaksi di cabangnya.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode' => 'USER',
                'nama' => 'User/Jamaah',
                'keterangan' => 'User umum yang dapat melakukan booking aula di semua cabang. Dapat melihat jadwal tersedia, membuat booking, dan melakukan pembayaran.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        $this->command->info('✅ Roles seeded successfully!');
        $this->command->table(
            ['Kode', 'Nama', 'Keterangan'],
            collect($roles)->map(function ($role) {
                return [
                    $role['kode'],
                    $role['nama'],
                    substr($role['keterangan'], 0, 50) . '...'
                ];
            })
        );
    }
}