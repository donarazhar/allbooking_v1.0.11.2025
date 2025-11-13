<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Cabang;
use App\Models\Catering;

class CateringSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all cabang
        $cabangList = Cabang::all();

        if ($cabangList->isEmpty()) {
            $this->command->warn('⚠️  No cabang found. Please run CabangSeeder first.');
            return;
        }

        $this->command->info('Creating catering data...');

        // Catering data dengan variasi coverage
        $cateringData = [
            // ✅ Catering Besar (Melayani Semua Cabang)
            [
                'nama' => 'Catering Mawar Sari',
                'email' => 'mawar.sari@catering.com',
                'no_hp' => '081234567890',
                'alamat' => 'Jl. Sudirman No. 123, Jakarta Pusat',
                'keterangan' => 'Catering profesional dengan pengalaman 10 tahun melayani berbagai acara besar',
                'coverage' => 'all', // Semua cabang
            ],
            [
                'nama' => 'Nusantara Catering',
                'email' => 'info@nusantara-catering.com',
                'no_hp' => '081234567891',
                'alamat' => 'Jl. Gatot Subroto No. 45, Jakarta Selatan',
                'keterangan' => 'Spesialis masakan nusantara dengan menu variatif',
                'coverage' => 'all', // Semua cabang
            ],

            // ✅ Catering Regional (3-4 Cabang)
            [
                'nama' => 'Berkah Catering',
                'email' => 'berkah@catering.com',
                'no_hp' => '081234567892',
                'alamat' => 'Jl. Asia Afrika No. 78, Bandung',
                'keterangan' => 'Catering dengan menu tradisional dan modern',
                'coverage' => 'regional', // 3-4 cabang
            ],
            [
                'nama' => 'Dapur Ibu Catering',
                'email' => 'dapur.ibu@catering.com',
                'no_hp' => '081234567893',
                'alamat' => 'Jl. Diponegoro No. 56, Semarang',
                'keterangan' => 'Masakan rumahan dengan cita rasa istimewa',
                'coverage' => 'regional', // 3-4 cabang
            ],
            [
                'nama' => 'Sari Rasa Catering',
                'email' => 'sari.rasa@catering.com',
                'no_hp' => '081234567894',
                'alamat' => 'Jl. Pemuda No. 89, Surabaya',
                'keterangan' => 'Catering untuk acara pernikahan dan corporate',
                'coverage' => 'regional', // 3-4 cabang
            ],

            // ✅ Catering Lokal (1-2 Cabang)
            [
                'nama' => 'Sedap Catering',
                'email' => 'sedap@catering.com',
                'no_hp' => '081234567895',
                'alamat' => 'Jl. Raya Bogor No. 234, Bogor',
                'keterangan' => 'Catering lokal dengan harga terjangkau',
                'coverage' => 'local', // 1-2 cabang
            ],
            [
                'nama' => 'Lezat Jaya Catering',
                'email' => 'lezat.jaya@catering.com',
                'no_hp' => '081234567896',
                'alamat' => 'Jl. Ahmad Yani No. 67, Bekasi',
                'keterangan' => 'Menu prasmanan dan box untuk berbagai acara',
                'coverage' => 'local', // 1-2 cabang
            ],
            [
                'nama' => 'Cahaya Catering',
                'email' => 'cahaya@catering.com',
                'no_hp' => '081234567897',
                'alamat' => 'Jl. Kemang No. 12, Jakarta Selatan',
                'keterangan' => 'Spesialis catering untuk acara keluarga',
                'coverage' => 'local', // 1-2 cabang
            ],
            [
                'nama' => 'Barokah Catering',
                'email' => 'barokah@catering.com',
                'no_hp' => '081234567898',
                'alamat' => 'Jl. Veteran No. 34, Tangerang',
                'keterangan' => 'Catering halal dengan sertifikasi MUI',
                'coverage' => 'local', // 1-2 cabang
            ],
            [
                'nama' => 'Wijaya Kusuma Catering',
                'email' => 'wijaya.kusuma@catering.com',
                'no_hp' => '081234567899',
                'alamat' => 'Jl. Raya Cikarang No. 45, Bekasi',
                'keterangan' => 'Menu buffet dan live cooking tersedia',
                'coverage' => 'local', // 1-2 cabang
            ],

            // ✅ Catering Multi-Regional (2-3 Cabang)
            [
                'nama' => 'Anugerah Catering',
                'email' => 'anugerah@catering.com',
                'no_hp' => '081234567800',
                'alamat' => 'Jl. Merdeka No. 90, Jakarta Barat',
                'keterangan' => 'Catering dengan layanan dekorasi pelaminan',
                'coverage' => 'multi', // 2-3 cabang
            ],
            [
                'nama' => 'Nikmat Catering',
                'email' => 'nikmat@catering.com',
                'no_hp' => '081234567801',
                'alamat' => 'Jl. Pahlawan No. 23, Depok',
                'keterangan' => 'Menu international dan tradisional',
                'coverage' => 'multi', // 2-3 cabang
            ],
            [
                'nama' => 'Harmoni Catering',
                'email' => 'harmoni@catering.com',
                'no_hp' => '081234567802',
                'alamat' => 'Jl. Proklamasi No. 56, Jakarta Timur',
                'keterangan' => 'Catering premium dengan chef berpengalaman',
                'coverage' => 'multi', // 2-3 cabang
            ],
        ];

        foreach ($cateringData as $data) {
            $coverage = $data['coverage'];
            unset($data['coverage']); // Remove coverage from insert data

            // Create catering
            $catering = Catering::create([
                'nama' => $data['nama'],
                'email' => $data['email'],
                'no_hp' => $data['no_hp'],
                'alamat' => $data['alamat'],
                'password' => Hash::make('password123'), // Default password
                'foto' => null,
                'keterangan' => $data['keterangan'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Attach catering to cabang based on coverage
            $cabangIds = [];

            switch ($coverage) {
                case 'all':
                    // Melayani semua cabang
                    $cabangIds = $cabangList->pluck('id')->toArray();
                    $this->command->info("  ✅ {$catering->nama}: Melayani SEMUA cabang (" . count($cabangIds) . " cabang)");
                    break;

                case 'regional':
                    // Melayani 3-4 cabang (random)
                    $count = rand(3, min(4, $cabangList->count()));
                    $cabangIds = $cabangList->random($count)->pluck('id')->toArray();
                    $this->command->info("  ✅ {$catering->nama}: Melayani {$count} cabang");
                    break;

                case 'multi':
                    // Melayani 2-3 cabang (random)
                    $count = rand(2, min(3, $cabangList->count()));
                    $cabangIds = $cabangList->random($count)->pluck('id')->toArray();
                    $this->command->info("  ✅ {$catering->nama}: Melayani {$count} cabang");
                    break;

                case 'local':
                    // Melayani 1-2 cabang (random)
                    $count = rand(1, min(2, $cabangList->count()));
                    $cabangIds = $cabangList->random($count)->pluck('id')->toArray();
                    $this->command->info("  ✅ {$catering->nama}: Melayani {$count} cabang");
                    break;
            }

            // Attach to pivot table (cabang_catering)
            foreach ($cabangIds as $cabangId) {
                DB::table('cabang_catering')->insert([
                    'cabang_id' => $cabangId,
                    'catering_id' => $catering->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('');
        $this->command->info('🎉 Catering seeding completed!');
        $this->command->info('');

        // Show summary
        $totalCatering = Catering::count();
        $this->command->info("📊 Summary:");
        $this->command->info("   Total Catering: {$totalCatering}");
        $this->command->info("   Default Password: password123");
        $this->command->info('');

        foreach ($cabangList as $cabang) {
            $cateringCount = DB::table('cabang_catering')
                ->where('cabang_id', $cabang->id)
                ->count();
            $this->command->info("   {$cabang->nama}: {$cateringCount} catering");
        }
    }
}
