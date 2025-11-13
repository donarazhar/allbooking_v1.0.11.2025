<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Cabang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate table untuk development
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get roles dan cabang
        $superAdminRole = Role::where('kode', 'SUPERADMIN')->first();
        $adminRole = Role::where('kode', 'ADMIN')->first();
        $pimpinanRole = Role::where('kode', 'PIMPINAN')->first();
        $userRole = Role::where('kode', 'USER')->first();

        $cabangAgung = Cabang::where('kode', 'AGUNG')->first();
        $cabangBintaro = Cabang::where('kode', 'BINTARO')->first();
        $cabangCikarang = Cabang::where('kode', 'CIKARANG')->first();
        $cabangSentra = Cabang::where('kode', 'SENTRA')->first();

        $users = [
            // ============================================
            // SUPER ADMIN
            // ============================================
            [
                'nama' => 'Super Administrator',
                'email' => 'superadmin@alazhar.or.id',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567890',
                'alamat' => 'Kantor Pusat YPI Al Azhar Jakarta',
                'jenis_kelamin' => 'Laki-laki',
                'tgl_lahir' => '1980-01-15',
                'nik' => '3174051234567890',
                'provinsi_id' => '31',
                'provinsi_nama' => 'DKI Jakarta',
                'kabupaten_id' => '3174',
                'kabupaten_nama' => 'Kota Jakarta Selatan',
                'kecamatan_id' => '3174050',
                'kecamatan_nama' => 'Kebayoran Baru',
                'kelurahan_id' => '3174050001',
                'kelurahan_nama' => 'Senayan',
                'kode_pos' => '12190',
                'foto' => null,
                'role_id' => $superAdminRole->id,
                'cabang_id' => $cabangAgung->id, // Default ke cabang Agung
                'status_users' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ============================================
            // ADMIN PER CABANG
            // ============================================
            [
                'nama' => 'Admin Masjid Agung',
                'email' => 'admin.agung@alazhar.or.id',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567891',
                'alamat' => 'Jl. Sisingamangaraja, Kebayoran Baru',
                'jenis_kelamin' => 'Laki-laki',
                'tgl_lahir' => '1985-03-20',
                'nik' => '3174051234567891',
                'provinsi_id' => '31',
                'provinsi_nama' => 'DKI Jakarta',
                'kabupaten_id' => '3174',
                'kabupaten_nama' => 'Kota Jakarta Selatan',
                'kecamatan_id' => '3174050',
                'kecamatan_nama' => 'Kebayoran Baru',
                'kelurahan_id' => '3174050001',
                'kelurahan_nama' => 'Senayan',
                'kode_pos' => '12190',
                'foto' => null,
                'role_id' => $adminRole->id,
                'cabang_id' => $cabangAgung->id,
                'status_users' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Admin Masjid Bintaro',
                'email' => 'admin.bintaro@alazhar.or.id',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567892',
                'alamat' => 'Jl. Bintaro Utama 3A, Sektor 3A',
                'jenis_kelamin' => 'Perempuan',
                'tgl_lahir' => '1987-05-12',
                'nik' => '3674051234567892',
                'provinsi_id' => '36',
                'provinsi_nama' => 'Banten',
                'kabupaten_id' => '3674',
                'kabupaten_nama' => 'Kota Tangerang Selatan',
                'kecamatan_id' => '3674010',
                'kecamatan_nama' => 'Pondok Aren',
                'kelurahan_id' => '3674010001',
                'kelurahan_nama' => 'Pondok Aren',
                'kode_pos' => '15224',
                'foto' => null,
                'role_id' => $adminRole->id,
                'cabang_id' => $cabangBintaro->id,
                'status_users' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Admin Masjid Cikarang',
                'email' => 'admin.cikarang@alazhar.or.id',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567893',
                'alamat' => 'Jl. Fatmawati Raya, Lippo Cikarang',
                'jenis_kelamin' => 'Laki-laki',
                'tgl_lahir' => '1986-07-25',
                'nik' => '3216051234567893',
                'provinsi_id' => '32',
                'provinsi_nama' => 'Jawa Barat',
                'kabupaten_id' => '3216',
                'kabupaten_nama' => 'Kabupaten Bekasi',
                'kecamatan_id' => '3216090',
                'kecamatan_nama' => 'Cikarang Barat',
                'kelurahan_id' => '3216090001',
                'kelurahan_nama' => 'Lippo Cikarang',
                'kode_pos' => '17530',
                'foto' => null,
                'role_id' => $adminRole->id,
                'cabang_id' => $cabangCikarang->id,
                'status_users' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Admin Masjid Sentra',
                'email' => 'admin.sentra@alazhar.or.id',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567894',
                'alamat' => 'Jl. Kemang Timur Raya No. 1',
                'jenis_kelamin' => 'Perempuan',
                'tgl_lahir' => '1988-09-30',
                'nik' => '3174051234567894',
                'provinsi_id' => '31',
                'provinsi_nama' => 'DKI Jakarta',
                'kabupaten_id' => '3174',
                'kabupaten_nama' => 'Kota Jakarta Selatan',
                'kecamatan_id' => '3174080',
                'kecamatan_nama' => 'Mampang Prapatan',
                'kelurahan_id' => '3174080001',
                'kelurahan_nama' => 'Bangka',
                'kode_pos' => '12730',
                'foto' => null,
                'role_id' => $adminRole->id,
                'cabang_id' => $cabangSentra->id,
                'status_users' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ============================================
            // PIMPINAN PER CABANG
            // ============================================
            [
                'nama' => 'Pimpinan Masjid Agung',
                'email' => 'pimpinan.agung@alazhar.or.id',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567895',
                'alamat' => 'Jl. Sisingamangaraja, Kebayoran Baru',
                'jenis_kelamin' => 'Laki-laki',
                'tgl_lahir' => '1975-02-10',
                'nik' => '3174051234567895',
                'provinsi_id' => '31',
                'provinsi_nama' => 'DKI Jakarta',
                'kabupaten_id' => '3174',
                'kabupaten_nama' => 'Kota Jakarta Selatan',
                'kecamatan_id' => '3174050',
                'kecamatan_nama' => 'Kebayoran Baru',
                'kelurahan_id' => '3174050001',
                'kelurahan_nama' => 'Senayan',
                'kode_pos' => '12190',
                'foto' => null,
                'role_id' => $pimpinanRole->id,
                'cabang_id' => $cabangAgung->id,
                'status_users' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Pimpinan Masjid Bintaro',
                'email' => 'pimpinan.bintaro@alazhar.or.id',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567896',
                'alamat' => 'Jl. Bintaro Utama 3A, Sektor 3A',
                'jenis_kelamin' => 'Laki-laki',
                'tgl_lahir' => '1976-04-18',
                'nik' => '3674051234567896',
                'provinsi_id' => '36',
                'provinsi_nama' => 'Banten',
                'kabupaten_id' => '3674',
                'kabupaten_nama' => 'Kota Tangerang Selatan',
                'kecamatan_id' => '3674010',
                'kecamatan_nama' => 'Pondok Aren',
                'kelurahan_id' => '3674010001',
                'kelurahan_nama' => 'Pondok Aren',
                'kode_pos' => '15224',
                'foto' => null,
                'role_id' => $pimpinanRole->id,
                'cabang_id' => $cabangBintaro->id,
                'status_users' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Pimpinan Masjid Cikarang',
                'email' => 'pimpinan.cikarang@alazhar.or.id',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567897',
                'alamat' => 'Jl. Fatmawati Raya, Lippo Cikarang',
                'jenis_kelamin' => 'Laki-laki',
                'tgl_lahir' => '1977-06-22',
                'nik' => '3216051234567897',
                'provinsi_id' => '32',
                'provinsi_nama' => 'Jawa Barat',
                'kabupaten_id' => '3216',
                'kabupaten_nama' => 'Kabupaten Bekasi',
                'kecamatan_id' => '3216090',
                'kecamatan_nama' => 'Cikarang Barat',
                'kelurahan_id' => '3216090001',
                'kelurahan_nama' => 'Lippo Cikarang',
                'kode_pos' => '17530',
                'foto' => null,
                'role_id' => $pimpinanRole->id,
                'cabang_id' => $cabangCikarang->id,
                'status_users' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Pimpinan Masjid Sentra',
                'email' => 'pimpinan.sentra@alazhar.or.id',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567898',
                'alamat' => 'Jl. Kemang Timur Raya No. 1',
                'jenis_kelamin' => 'Laki-laki',
                'tgl_lahir' => '1978-08-14',
                'nik' => '3174051234567898',
                'provinsi_id' => '31',
                'provinsi_nama' => 'DKI Jakarta',
                'kabupaten_id' => '3174',
                'kabupaten_nama' => 'Kota Jakarta Selatan',
                'kecamatan_id' => '3174080',
                'kecamatan_nama' => 'Mampang Prapatan',
                'kelurahan_id' => '3174080001',
                'kelurahan_nama' => 'Bangka',
                'kode_pos' => '12730',
                'foto' => null,
                'role_id' => $pimpinanRole->id,
                'cabang_id' => $cabangSentra->id,
                'status_users' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ============================================
            // USER / JAMAAH (Sample)
            // ============================================
            [
                'nama' => 'Ahmad Jamaludin',
                'email' => 'ahmad.jamaludin@gmail.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567899',
                'alamat' => 'Jl. Raya Pasar Minggu No. 45',
                'jenis_kelamin' => 'Laki-laki',
                'tgl_lahir' => '1990-01-05',
                'nik' => '3174051990010501',
                'provinsi_id' => '31',
                'provinsi_nama' => 'DKI Jakarta',
                'kabupaten_id' => '3174',
                'kabupaten_nama' => 'Kota Jakarta Selatan',
                'kecamatan_id' => '3174020',
                'kecamatan_nama' => 'Pasar Minggu',
                'kelurahan_id' => '3174020001',
                'kelurahan_nama' => 'Pasar Minggu',
                'kode_pos' => '12510',
                'foto' => null,
                'role_id' => $userRole->id,
                'cabang_id' => $cabangAgung->id, // User bisa booking di semua cabang
                'status_users' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Siti Nurhaliza',
                'email' => 'siti.nurhaliza@gmail.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567800',
                'alamat' => 'Jl. Bintaro Jaya Sektor 9',
                'jenis_kelamin' => 'Perempuan',
                'tgl_lahir' => '1992-03-15',
                'nik' => '3674051992031502',
                'provinsi_id' => '36',
                'provinsi_nama' => 'Banten',
                'kabupaten_id' => '3674',
                'kabupaten_nama' => 'Kota Tangerang Selatan',
                'kecamatan_id' => '3674010',
                'kecamatan_nama' => 'Pondok Aren',
                'kelurahan_id' => '3674010002',
                'kelurahan_nama' => 'Jurang Mangu Barat',
                'kode_pos' => '15222',
                'foto' => null,
                'role_id' => $userRole->id,
                'cabang_id' => $cabangBintaro->id,
                'status_users' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Budi Santoso',
                'email' => 'budi.santoso@gmail.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567801',
                'alamat' => 'Jl. Cikarang Baru No. 123',
                'jenis_kelamin' => 'Laki-laki',
                'tgl_lahir' => '1988-07-20',
                'nik' => '3216051988072003',
                'provinsi_id' => '32',
                'provinsi_nama' => 'Jawa Barat',
                'kabupaten_id' => '3216',
                'kabupaten_nama' => 'Kabupaten Bekasi',
                'kecamatan_id' => '3216090',
                'kecamatan_nama' => 'Cikarang Barat',
                'kelurahan_id' => '3216090002',
                'kelurahan_nama' => 'Cikarang Baru',
                'kode_pos' => '17530',
                'foto' => null,
                'role_id' => $userRole->id,
                'cabang_id' => $cabangCikarang->id,
                'status_users' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Dewi Lestari',
                'email' => 'dewi.lestari@gmail.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567802',
                'alamat' => 'Jl. Kemang Raya No. 88',
                'jenis_kelamin' => 'Perempuan',
                'tgl_lahir' => '1991-11-10',
                'nik' => '3174051991111004',
                'provinsi_id' => '31',
                'provinsi_nama' => 'DKI Jakarta',
                'kabupaten_id' => '3174',
                'kabupaten_nama' => 'Kota Jakarta Selatan',
                'kecamatan_id' => '3174080',
                'kecamatan_nama' => 'Mampang Prapatan',
                'kelurahan_id' => '3174080002',
                'kelurahan_nama' => 'Pela Mampang',
                'kode_pos' => '12720',
                'foto' => null,
                'role_id' => $userRole->id,
                'cabang_id' => $cabangSentra->id,
                'status_users' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Muhammad Rizki',
                'email' => 'muhammad.rizki@gmail.com',
                'password' => Hash::make('password123'),
                'no_hp' => '081234567803',
                'alamat' => 'Jl. TB Simatupang No. 99',
                'jenis_kelamin' => 'Laki-laki',
                'tgl_lahir' => '1993-05-25',
                'nik' => '3174051993052505',
                'provinsi_id' => '31',
                'provinsi_nama' => 'DKI Jakarta',
                'kabupaten_id' => '3174',
                'kabupaten_nama' => 'Kota Jakarta Selatan',
                'kecamatan_id' => '3174090',
                'kecamatan_nama' => 'Jagakarsa',
                'kelurahan_id' => '3174090001',
                'kelurahan_nama' => 'Ciganjur',
                'kode_pos' => '12630',
                'foto' => null,
                'role_id' => $userRole->id,
                'cabang_id' => $cabangAgung->id,
                'status_users' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info('✅ Users seeded successfully!');
        $this->command->newLine();
        
        // Summary table
        $this->command->info('📊 User Summary by Role:');
        $this->command->table(
            ['Role', 'Total Users'],
            [
                ['Super Admin', User::whereHas('role', fn($q) => $q->where('kode', 'SUPERADMIN'))->count()],
                ['Admin Cabang', User::whereHas('role', fn($q) => $q->where('kode', 'ADMIN'))->count()],
                ['Pimpinan Cabang', User::whereHas('role', fn($q) => $q->where('kode', 'PIMPINAN'))->count()],
                ['User/Jamaah', User::whereHas('role', fn($q) => $q->where('kode', 'USER'))->count()],
            ]
        );

        $this->command->newLine();
        $this->command->info('🔑 Default Password: password123');
        $this->command->warn('⚠️  Don\'t forget to change passwords in production!');
    }
}