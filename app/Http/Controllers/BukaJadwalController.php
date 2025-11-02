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
        $bukaJadwal = BukaJadwal::with(['sesi', 'jenisAcara'])
            ->orderBy('tanggal', 'desc')
            ->get();
        return view('transaksi.buka-jadwal.index', compact('bukaJadwal'));
    }

    public function store(Request $request)
    {
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

        BukaJadwal::create($validated);

        return redirect()->route('buka-jadwal.index')
            ->with('success', 'Buka jadwal berhasil ditambahkan!');
    }

    public function update(Request $request, BukaJadwal $bukaJadwal)
    {
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

        $bukaJadwal->update($validated);

        return redirect()->route('buka-jadwal.index')
            ->with('success', 'Buka jadwal berhasil diupdate!');
    }

    public function destroy(BukaJadwal $bukaJadwal)
    {
        try {
            $bukaJadwal->delete();
            return redirect()->route('buka-jadwal.index')
                ->with('success', 'Buka jadwal berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('buka-jadwal.index')
                ->with('error', 'Buka jadwal tidak dapat dihapus karena sudah dibooking!');
        }
    }
}
