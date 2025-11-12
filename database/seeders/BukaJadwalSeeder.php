<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BukaJadwal;
use App\Models\JenisAcara;
use App\Models\Sesi;
use Carbon\Carbon;

class BukaJadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jenisAcaraIds = JenisAcara::pluck('id')->all();
        $sesiIds = Sesi::pluck('id')->all();

        if (empty($jenisAcaraIds) || empty($sesiIds)) {
            $this->command->warn('Peringatan: Tidak ada data di tabel JenisAcara atau Sesi. BukaJadwalSeeder tidak dapat dijalankan.');
            return;
        }

        $createdCombinations = [];
        $recordsToCreate = 100;
        $startDate = Carbon::create(2025, 11, 1);
        Carbon::setLocale('id');

        $this->command->info('Membuat 100 jadwal unik...');

        for ($i = 0; $i < $recordsToCreate; $i++) {
            $attempt = 0;
            do {
                // Distribusikan pembuatan data secara acak di seluruh rentang 12 bulan
                $randomMonth = rand(0, 11);
                $currentDate = $startDate->copy()->addMonths($randomMonth);
                
                $daysInMonth = $currentDate->daysInMonth;
                $randomDay = rand(1, $daysInMonth);
                $tanggal = Carbon::create($currentDate->year, $currentDate->month, $randomDay);
                
                $sesiId = $sesiIds[array_rand($sesiIds)];
                $jenisAcaraId = $jenisAcaraIds[array_rand($jenisAcaraIds)];

                $key = $tanggal->toDateString() . '-' . $sesiId . '-' . $jenisAcaraId;

                $attempt++;
                if ($attempt > 500) { // Pengaman jika terlalu sulit menemukan kombinasi unik
                    $this->command->error('Gagal menemukan kombinasi jadwal unik setelah 500 percobaan. Mungkin tidak ada cukup variasi JenisAcara/Sesi. Seeder dihentikan.');
                    return;
                }

            } while (isset($createdCombinations[$key])); // Ulangi terus jika kombinasi sudah pernah dibuat

            // Tandai kombinasi ini sebagai sudah digunakan
            $createdCombinations[$key] = true;

            // Buat data di database dengan data yang dijamin unik
            BukaJadwal::create([
                'hari' => $tanggal->translatedFormat('l'),
                'tanggal' => $tanggal->toDateString(),
                'sesi_id' => $sesiId,
                'jenisacara_id' => $jenisAcaraId,
                'status_jadwal' => 'available', // Sesuai dengan pesan error Anda
            ]);
        }

        $this->command->info('Berhasil membuat 100 jadwal unik.');
    }
}
