<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use App\Models\BukaJadwal;
use Carbon\Carbon;
use Faker\Factory as Faker;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');

        $jadwals = BukaJadwal::where('status_jadwal', 'available')->inRandomOrder()->limit(50)->get();
        $userIds = User::where('role_id', 3)->pluck('id')->all();

        if ($jadwals->count() < 50 || empty($userIds)) {
            $this->command->warn('Tidak cukup data Jadwal (tersedia: '. $jadwals->count() . ') atau User untuk membuat 50 booking. Seeder dibatalkan.');
            return;
        }

        $this->command->info('Membuat 50 data booking dengan status inactive dan masa berlaku 14 hari...');

        foreach ($jadwals as $jadwal) {
            $tglBooking = Carbon::now();

            Booking::create([
                'user_id' => $userIds[array_rand($userIds)],
                'bukajadwal_id' => $jadwal->id,
                'tgl_booking' => $tglBooking->toDateString(),
                'catering_id' => null,
                'status_booking' => 'inactive',
                'keterangan' => $faker->sentence(3),
                'tgl_expired_booking' => $tglBooking->copy()->addDays(14)->toDateString(), // --- DIPERBAIKI: Masa berlaku 14 hari
            ]);

            $jadwal->status_jadwal = 'booked';
            $jadwal->save();
        }

        $this->command->info('Berhasil membuat 50 data booking.');
    }
}
