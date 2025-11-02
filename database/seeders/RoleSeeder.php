<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus semua data lama
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Role::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create Roles
        $adminRole = Role::create([
            'kode' => 'ADM',
            'nama' => 'Admin',
            'keterangan' => 'Administrator dengan akses penuh ke semua fitur sistem'
        ]);

        $pimpinanRole = Role::create([
            'kode' => 'PMP',
            'nama' => 'Pimpinan',
            'keterangan' => 'Pimpinan yang dapat melihat laporan dan dashboard'
        ]);

        $userRole = Role::create([
            'kode' => 'USR',
            'nama' => 'User',
            'keterangan' => 'Pengguna/Klien yang dapat melakukan booking dan pembayaran'
        ]);

        // Create Users dengan role yang benar
        User::create([
            'nama' => 'Administrator',
            'email' => 'admin@aula.com',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRole->id,
            'status_users' => 'active'
        ]);

        User::create([
            'nama' => 'Pimpinan',
            'email' => 'pimpinan@aula.com',
            'password' => Hash::make('pimpinan123'),
            'role_id' => $pimpinanRole->id,
            'status_users' => 'active'
        ]);

        User::create([
            'nama' => 'User Demo',
            'email' => 'user@aula.com',
            'password' => Hash::make('user123'),
            'role_id' => $userRole->id,
            'status_users' => 'active'
        ]);

        echo "✓ Roles created: {$adminRole->id}, {$pimpinanRole->id}, {$userRole->id}\n";
        echo "✓ Users created successfully\n";
    }
}
