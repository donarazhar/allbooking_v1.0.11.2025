<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get role ID for 'user' (bukan admin)
        $userRole = Role::where('nama', 'user')->first();
        
        if (!$userRole) {
            $this->command->error('Role "user" tidak ditemukan! Pastikan RoleSeeder sudah dijalankan.');
            return;
        }

        $users = [
            [
                'nama' => 'Budi Santoso',
                'email' => 'budi.santoso@email.com',
                'password' => Hash::make('password'),
                'no_hp' => '081234567001',
                'alamat' => 'Jl. Mawar No. 10, Jakarta Timur',
                'role_id' => $userRole->id,
                'status_users' => 'active'
            ],
            [
                'nama' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@email.com',
                'password' => Hash::make('password'),
                'no_hp' => '081234567002',
                'alamat' => 'Jl. Melati No. 15, Jakarta Barat',
                'role_id' => $userRole->id,
               'status_users' => 'active'
            ],
            
        ];

        foreach ($users as $data) {
            User::create($data);
        }

        $this->command->info('UserSeeder berhasil! 2 user created dengan password: password');
    }
}
