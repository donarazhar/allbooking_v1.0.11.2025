<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Inisialisasi Faker untuk membuat data palsu
        $faker = Faker::create('id_ID');

        // Loop untuk membuat 100 data pengguna baru dengan peran 'User'
        for ($i = 0; $i < 100; $i++) {
            User::create([
                'nama' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'), // Password default untuk semua user baru
                'role_id' => 3, // ID untuk peran 'User'
                'status_users' => 'active',
                'alamat' => $faker->address,
                'no_hp' => $faker->phoneNumber,
                'jenis_kelamin' => $faker->randomElement(['Laki-laki', 'Perempuan']),
                'tgl_lahir' => $faker->date(),
            ]);
        }
    }
}
