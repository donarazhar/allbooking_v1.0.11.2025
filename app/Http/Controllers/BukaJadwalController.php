<?php

namespace App\Http\Controllers;

use App\Models\BukaJadwal;
use App\Models\Sesi;
use App\Models\JenisAcara;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BukaJadwalController extends Controller
{
    public function index()
    {
        $bukaJadwal = BukaJadwal::with(['sesi', 'jenisAcara'])
            ->withCount('bookings')
            ->orderBy('tanggal', 'desc')
            ->get();
            
        return view('transaksi.buka-jadwal.index', compact('bukaJadwal'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'tanggal' => 'required|date|after_or_equal:today',
            'sesi_id' => 'required|exists:sesi,id',
            'jenisacara_id' => 'required|exists:jenis_acara,id',
            'status_jadwal' => 'required|in:available,booked'
        ], [
            'hari.required' => 'Hari harus diisi',
            'hari.in' => 'Hari tidak valid',
            'tanggal.required' => 'Tanggal harus diisi',
            'tanggal.after_or_equal' => 'Tanggal tidak boleh kurang dari hari ini',
            'sesi_id.required' => 'Sesi harus dipilih',
            'jenisacara_id.required' => 'Jenis acara harus dipilih',
            'status_jadwal.required' => 'Status jadwal harus dipilih'
        ]);

        // Auto-set hari dari tanggal
        $tanggal = Carbon::parse($validated['tanggal']);
        $hariDariTanggal = $tanggal->locale('id')->dayName;
        
        // Mapping hari Indonesia
        $hariMapping = [
            'Senin' => 'Senin',
            'Selasa' => 'Selasa',
            'Rabu' => 'Rabu',
            'Kamis' => 'Kamis',
            'Jumat' => 'Jumat',
            'Sabtu' => 'Sabtu',
            'Minggu' => 'Minggu'
        ];
        
        $validated['hari'] = $hariMapping[$hariDariTanggal] ?? $validated['hari'];

        // Check duplicate jadwal (tanggal + sesi yang sama)
        $exists = BukaJadwal::where('tanggal', $validated['tanggal'])
            ->where('sesi_id', $validated['sesi_id'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Jadwal dengan tanggal dan sesi yang sama sudah ada!')
                ->withInput();
        }

        try {
            BukaJadwal::create($validated);
            return redirect()->route('admin.transaksi.buka-jadwal.index')
                ->with('success', 'Buka jadwal berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error creating buka jadwal: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan jadwal.')
                ->withInput();
        }
    }

    public function update(Request $request, BukaJadwal $bukaJadwal)
    {
        $validated = $request->validate([
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'tanggal' => 'required|date',
            'sesi_id' => 'required|exists:sesi,id',
            'jenisacara_id' => 'required|exists:jenis_acara,id',
            'status_jadwal' => 'required|in:available,booked'
        ], [
            'hari.required' => 'Hari harus diisi',
            'tanggal.required' => 'Tanggal harus diisi',
            'sesi_id.required' => 'Sesi harus dipilih',
            'jenisacara_id.required' => 'Jenis acara harus dipilih',
            'status_jadwal.required' => 'Status jadwal harus dipilih'
        ]);

        // Check: Jika sudah ada booking, status TIDAK bisa diubah ke available
        $hasBooking = $bukaJadwal->bookings()->count() > 0;
        
        if ($hasBooking && $validated['status_jadwal'] === 'available') {
            return redirect()->back()
                ->with('error', 'Status tidak dapat diubah ke Available karena jadwal sudah memiliki booking!')
                ->withInput();
        }

        // Auto-set hari dari tanggal
        $tanggal = Carbon::parse($validated['tanggal']);
        $hariDariTanggal = $tanggal->locale('id')->dayName;
        
        $hariMapping = [
            'Senin' => 'Senin',
            'Selasa' => 'Selasa',
            'Rabu' => 'Rabu',
            'Kamis' => 'Kamis',
            'Jumat' => 'Jumat',
            'Sabtu' => 'Sabtu',
            'Minggu' => 'Minggu'
        ];
        
        $validated['hari'] = $hariMapping[$hariDariTanggal] ?? $validated['hari'];

        // Check duplicate (exclude current record)
        $exists = BukaJadwal::where('tanggal', $validated['tanggal'])
            ->where('sesi_id', $validated['sesi_id'])
            ->where('id', '!=', $bukaJadwal->id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Jadwal dengan tanggal dan sesi yang sama sudah ada!')
                ->withInput();
        }

        // Validasi status: Jika ada booking aktif, tidak bisa ubah ke available
        $hasActiveBooking = $bukaJadwal->bookings()
            ->where('status_booking', 'active')
            ->exists();

        if ($hasActiveBooking && $validated['status_jadwal'] === 'available') {
            return redirect()->back()
                ->with('error', 'Tidak dapat mengubah status menjadi available karena jadwal ini masih memiliki booking aktif!')
                ->withInput();
        }

        try {
            $bukaJadwal->update($validated);
            return redirect()->route('admin.transaksi.buka-jadwal.index')
                ->with('success', 'Buka jadwal berhasil diupdate!');
        } catch (\Exception $e) {
            Log::error('Error updating buka jadwal: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengupdate jadwal.')
                ->withInput();
        }
    }

    public function destroy(BukaJadwal $bukaJadwal)
    {
        $bookingCount = $bukaJadwal->bookings()->count();
        
        if ($bookingCount > 0) {
            return redirect()->route('admin.transaksi.buka-jadwal.index')
                ->with('error', "Jadwal tidak dapat dihapus karena sudah digunakan oleh {$bookingCount} booking!");
        }

        try {
            $tanggal = Carbon::parse($bukaJadwal->tanggal)->format('d/m/Y');
            $sesi = $bukaJadwal->sesi->nama ?? '-';
            $bukaJadwal->delete();
            
            return redirect()->route('admin.transaksi.buka-jadwal.index')
                ->with('success', "Jadwal {$tanggal} - {$sesi} berhasil dihapus!");
        } catch (\Exception $e) {
            Log::error('Error deleting buka jadwal: ' . $e->getMessage());
            return redirect()->route('admin.transaksi.buka-jadwal.index')
                ->with('error', 'Terjadi kesalahan saat menghapus jadwal.');
        }
    }
}