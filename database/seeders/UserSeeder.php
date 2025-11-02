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
                'password' => Hash::make('password123'),
                'no_hp' => '081234567001',
                'alamat' => 'Jl. Mawar No. 10, Jakarta Timur',
                'role_id' => $userRole->id,
                'status_users' => 'active'
            ],
            [
                'nama' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@email.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567002',
                'alamat' => 'Jl. Melati No. 15, Jakarta Barat',
                'role_id' => $userRole->id,
                'status_users' => 'active'
            ],
            [
                'nama' => 'Andi Wijaya',
                'email' => 'andi.wijaya@email.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567003',
                'alamat' => 'Jl. Anggrek No. 20, Jakarta Selatan',
                'role_id' => $userRole->id,
                'status_users' => 'active'
            ],
            [
                'nama' => 'Rina Kusuma',
                'email' => 'rina.kusuma@email.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567004',
                'alamat' => 'Jl. Dahlia No. 25, Jakarta Pusat',
                'role_id' => $userRole->id,
                'status_users' => 'active'
            ],
            [
                'nama' => 'Dewi Lestari',
                'email' => 'dewi.lestari@email.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567005',
                'alamat' => 'Jl. Kenanga No. 30, Jakarta Utara',
                'role_id' => $userRole->id,
                'status_users' => 'active'
            ],
            [
                'nama' => 'Ahmad Fadli',
                'email' => 'ahmad.fadli@email.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567006',
                'alamat' => 'Jl. Teratai No. 35, Tangerang',
                'role_id' => $userRole->id,
                'status_users' => 'active'
            ],
            [
                'nama' => 'Putri Ayu',
                'email' => 'putri.ayu@email.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567007',
                'alamat' => 'Jl. Cempaka No. 40, Bekasi',
                'role_id' => $userRole->id,
                'status_users' => 'active'
            ],
            [
                'nama' => 'Hendra Gunawan',
                'email' => 'hendra.gunawan@email.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567008',
                'alamat' => 'Jl. Flamboyan No. 45, Depok',
                'role_id' => $userRole->id,
                'status_users' => 'active'
            ],
            [
                'nama' => 'Lisa Permata',
                'email' => 'lisa.permata@email.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567009',
                'alamat' => 'Jl. Sakura No. 50, Bogor',
                'role_id' => $userRole->id,
                'status_users' => 'active'
            ],
            [
                'nama' => 'Rudi Hartono',
                'email' => 'rudi.hartono@email.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567010',
                'alamat' => 'Jl. Seruni No. 55, Jakarta Timur',
                'role_id' => $userRole->id,
                'status_users' => 'active'
            ],
            [
                'nama' => 'Maya Sari',
                'email' => 'maya.sari@email.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567011',
                'alamat' => 'Jl. Tulip No. 60, Jakarta Barat',
                'role_id' => $userRole->id,
                'status_users' => 'active'
            ],
            [
                'nama' => 'Farhan Maulana',
                'email' => 'farhan.maulana@email.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567012',
                'alamat' => 'Jl. Kamboja No. 65, Jakarta Selatan',
                'role_id' => $userRole->id,
                'status_users' => 'active'
            ],
            [
                'nama' => 'Indah Purnama',
                'email' => 'indah.purnama@email.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567013',
                'alamat' => 'Jl. Azalea No. 70, Tangerang Selatan',
                'role_id' => $userRole->id,
                'status_users' => 'active'
            ],
            [
                'nama' => 'Yusuf Ibrahim',
                'email' => 'yusuf.ibrahim@email.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567014',
                'alamat' => 'Jl. Lily No. 75, Bekasi Barat',
                'role_id' => $userRole->id,
                'status_users' => 'active'
            ],
            [
                'nama' => 'Fitri Handayani',
                'email' => 'fitri.handayani@email.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567015',
                'alamat' => 'Jl. Lavender No. 80, Depok',
                'role_id' => $userRole->id,
                'status_users' => 'active'
            ]
        ];

        foreach ($users as $data) {
            User::create($data);
        }

        $this->command->info('UserSeeder berhasil! 15 user created dengan password: password123');
    }
}
