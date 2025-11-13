<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Cabang;
use App\Models\Sesi;
use App\Models\JenisAcara;
use Carbon\Carbon;

class BukaJadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all cabang
        $cabangList = Cabang::all();

        // Hari-hari dalam bahasa Indonesia
        $hariIndonesia = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        // Loop untuk setiap cabang
        foreach ($cabangList as $cabang) {
            $this->command->info("Processing cabang: {$cabang->nama}");

            // Get sesi dan jenis acara untuk cabang ini
            $sesiList = Sesi::where('cabang_id', $cabang->id)->pluck('id')->toArray();
            $jenisAcaraList = JenisAcara::where('cabang_id', $cabang->id)->pluck('id')->toArray();

            if (empty($sesiList) || empty($jenisAcaraList)) {
                $this->command->warn("⚠️  Skipping {$cabang->nama} - No sesi or jenis acara found");
                continue;
            }

            $jadwalCount = 0;

            // Loop untuk 12 bulan (November 2025 - Oktober 2026)
            for ($month = 0; $month < 12; $month++) {
                $startDate = Carbon::create(2025, 11, 1)->addMonths($month); // Start from Nov 2025
                $daysInMonth = $startDate->daysInMonth;

                // Generate 15 jadwal per bulan
                $datesInMonth = range(1, $daysInMonth);
                shuffle($datesInMonth); // Random order
                $selectedDates = array_slice($datesInMonth, 0, 15); // Ambil 15 tanggal random
                sort($selectedDates); // Sort untuk lebih rapi

                foreach ($selectedDates as $day) {
                    $tanggal = Carbon::create($startDate->year, $startDate->month, $day);

                    // Skip jika tanggal sudah lewat (untuk keamanan)
                    if ($tanggal->isPast()) {
                        continue;
                    }

                    // Random sesi dan jenis acara
                    $sesiId = $sesiList[array_rand($sesiList)];
                    $jenisAcaraId = $jenisAcaraList[array_rand($jenisAcaraList)];

                    // Get hari dalam bahasa Indonesia
                    $hari = $hariIndonesia[$tanggal->englishDayOfWeek];

                    // Check duplicate (tanggal + sesi yang sama)
                    $exists = DB::table('buka_jadwal')
                        ->where('cabang_id', $cabang->id)
                        ->where('tanggal', $tanggal->format('Y-m-d'))
                        ->where('sesi_id', $sesiId)
                        ->exists();

                    if (!$exists) {
                        DB::table('buka_jadwal')->insert([
                            'cabang_id' => $cabang->id,
                            'hari' => $hari,
                            'tanggal' => $tanggal->format('Y-m-d'),
                            'sesi_id' => $sesiId,
                            'jenisacara_id' => $jenisAcaraId,
                            'status_jadwal' => 'available',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);

                        $jadwalCount++;
                    }
                }

                $this->command->info("  → {$startDate->format('F Y')}: Generated");
            }

            $this->command->info("✅ {$cabang->nama}: {$jadwalCount} jadwal created\n");
        }

        $this->command->info('🎉 Buka Jadwal seeding completed for all cabang!');
    }
}
