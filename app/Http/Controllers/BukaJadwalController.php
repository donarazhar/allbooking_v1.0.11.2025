<?php

namespace App\Http\Controllers;

use App\Models\BukaJadwal;
use App\Models\Sesi;
use App\Models\JenisAcara;
use Illuminate\Http\Request;

class BukaJadwalController extends Controller
{
    
    public function index()
    {
        // Mengambil semua data jadwal, termasuk relasi ke 'sesi' dan 'jenisAcara'.
        // Diurutkan berdasarkan tanggal terbaru.
        $bukaJadwal = BukaJadwal::with(['sesi', 'jenisAcara'])
            ->orderBy('tanggal', 'desc')
            ->get();
            
        // Mengirim data ke view untuk ditampilkan.
        return view('transaksi.buka-jadwal.index', compact('bukaJadwal'));
    }

    /**
     * Menyimpan data jadwal baru yang diinput dari form.
     */
    public function store(Request $request)
    {
        // Memvalidasi data yang masuk dari request.
        $validated = $request->validate([
            'hari' => 'required|max:20',
            'tanggal' => 'required|date',
            'sesi_id' => 'required|exists:sesi,id',
            'jenisacara_id' => 'required|exists:jenis_acara,id'
        ], [
            // Pesan error kustom untuk validasi.
            'hari.required' => 'Hari harus diisi',
            'tanggal.required' => 'Tanggal harus diisi',
            'sesi_id.required' => 'Sesi harus dipilih',
            'jenisacara_id.required' => 'Jenis acara harus dipilih'
        ]);

        // Membuat record baru di database menggunakan data yang sudah divalidasi.
        BukaJadwal::create($validated);

        // Mengarahkan kembali ke halaman index dengan pesan sukses.
        return redirect()->route('buka-jadwal.index')
            ->with('success', 'Buka jadwal berhasil ditambahkan!');
    }

    /**
     * Mengupdate data jadwal yang sudah ada.
     */
    public function update(Request $request, BukaJadwal $bukaJadwal)
    {
        // Validasi data yang masuk, mirip seperti fungsi store.
        $validated = $request->validate([
            'hari' => 'required|max:20',
            'tanggal' => 'required|date',
            'sesi_id' => 'required|exists:sesi,id',
            'jenisacara_id' => 'required|exists:jenis_acara,id'
        ], [
            'hari.required' => 'Hari harus diisi',
            'tanggal.required' => 'Tanggal harus diisi',
            'sesi_id.required' => 'Sesi harus dipilih',
            'jenisacara_id.required' => 'Jenis acara harus dipilih'
        ]);

        // Mengupdate record yang ada dengan data yang sudah divalidasi.
        $bukaJadwal->update($validated);

        // Mengarahkan kembali ke halaman index dengan pesan sukses.
        return redirect()->route('buka-jadwal.index')
            ->with('success', 'Buka jadwal berhasil diupdate!');
    }

    /**
     * Menghapus data jadwal.
     */
    public function destroy(BukaJadwal $bukaJadwal)
    {
        try {
            // Mencoba untuk menghapus data.
            $bukaJadwal->delete();
            return redirect()->route('buka-jadwal.index')
                ->with('success', 'Buka jadwal berhasil dihapus!');
        } catch (\Exception $e) {
            // Jika terjadi error (misalnya karena jadwal sudah dibooking),
            // kirim pesan error.
            return redirect()->route('buka-jadwal.index')
                ->with('error', 'Buka jadwal tidak dapat dihapus karena sudah dibooking!');
        }
    }
}
