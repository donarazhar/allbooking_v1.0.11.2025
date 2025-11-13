<?php

namespace App\Http\Controllers;

use App\Models\BukaJadwal;
use App\Models\Sesi;
use App\Models\JenisAcara;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BukaJadwalController extends Controller
{
    /**
     * Display a listing of buka jadwal
     * 
     * Super Admin: Lihat SEMUA jadwal dari semua cabang (readonly)
     * Admin Cabang: Lihat jadwal cabangnya saja (CRUD)
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->role->kode === 'SUPERADMIN';

        // Base query
        $query = BukaJadwal::with(['sesi', 'jenisAcara', 'cabang'])
            ->withCount('transaksiBooking');

        // =====================
        // LOGIC: Filter berdasarkan role
        // =====================
        if ($isSuperAdmin) {
            // Super Admin: Lihat SEMUA jadwal dari semua cabang

            // Filter by cabang jika ada request
            if ($request->filled('cabang_id')) {
                $query->where('buka_jadwal.cabang_id', $request->cabang_id);
            }
        } else {
            // Admin Cabang: Hanya jadwal cabangnya
            $cabangId = $currentUser->cabang_id;
            $query->where('buka_jadwal.cabang_id', $cabangId);
        }

        // Search filter (untuk admin cabang)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('buka_jadwal.hari', 'like', "%{$search}%")
                    ->orWhere('buka_jadwal.tanggal', 'like', "%{$search}%")
                    ->orWhereHas('sesi', function ($q) use ($search) {
                        $q->where('sesi.nama', 'like', "%{$search}%");
                    })
                    ->orWhereHas('jenisAcara', function ($q) use ($search) {
                        $q->where('jenis_acara.nama', 'like', "%{$search}%");
                    });
            });
        }

        // Get buka jadwal data
        $bukaJadwal = $query->orderBy('buka_jadwal.tanggal', 'desc')->get();

        // Get cabang list untuk filter (Super Admin)
        $cabangList = Cabang::orderBy('nama')->get();

        // Get current cabang info (Admin Cabang)
        $cabangInfo = !$isSuperAdmin ? Cabang::find($currentUser->cabang_id) : null;

        // Get sesi & jenis acara untuk modal (Admin Cabang only)
        if (!$isSuperAdmin) {
            $sesiList = Sesi::where('cabang_id', $currentUser->cabang_id)
                ->orderBy('jam_mulai')
                ->get();
            $jenisAcaraList = JenisAcara::where('cabang_id', $currentUser->cabang_id)
                ->where('status_jenis_acara', 'active')
                ->orderBy('nama')
                ->get();
        } else {
            $sesiList = collect();
            $jenisAcaraList = collect();
        }

        return view('admin.transaksi.buka-jadwal.index', compact(
            'bukaJadwal',
            'cabangList',
            'cabangInfo',
            'isSuperAdmin',
            'sesiList',
            'jenisAcaraList'
        ));
    }

    /**
     * Store a newly created buka jadwal
     * Only Admin Cabang can create
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can create
        if ($currentUser->role->kode === 'SUPERADMIN') {
            abort(403, 'Super Admin tidak dapat menambah jadwal. Fitur ini khusus untuk Admin Cabang.');
        }

        $cabangId = $currentUser->cabang_id;

        $validated = $request->validate([
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'tanggal' => 'required|date|after_or_equal:today',
            'sesi_id' => [
                'required',
                'exists:sesi,id',
                // Sesi harus dari cabang yang sama
                function ($attribute, $value, $fail) use ($cabangId) {
                    $sesi = Sesi::find($value);
                    if ($sesi && $sesi->cabang_id !== $cabangId) {
                        $fail('Sesi tidak tersedia di cabang Anda.');
                    }
                },
            ],
            'jenisacara_id' => [
                'required',
                'exists:jenis_acara,id',
                // Jenis Acara harus dari cabang yang sama
                function ($attribute, $value, $fail) use ($cabangId) {
                    $jenisAcara = JenisAcara::find($value);
                    if ($jenisAcara && $jenisAcara->cabang_id !== $cabangId) {
                        $fail('Jenis acara tidak tersedia di cabang Anda.');
                    }
                },
            ],
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
        $hariMapping = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $validated['hari'] = $hariMapping[$tanggal->englishDayOfWeek] ?? $validated['hari'];

        // Check duplicate jadwal (tanggal + sesi yang sama di cabang yang sama)
        $exists = BukaJadwal::where('tanggal', $validated['tanggal'])
            ->where('sesi_id', $validated['sesi_id'])
            ->where('cabang_id', $cabangId)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Jadwal dengan tanggal dan sesi yang sama sudah ada di cabang Anda!')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $validated['cabang_id'] = $cabangId;
            BukaJadwal::create($validated);

            DB::commit();

            return redirect()->route('admin.transaksi.buka-jadwal.index')
                ->with('success', 'Buka jadwal berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating buka jadwal: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan jadwal.')
                ->withInput();
        }
    }

    /**
     * Update the specified buka jadwal
     * Only Admin Cabang can update their own jadwal
     */
    public function update(Request $request, BukaJadwal $bukaJadwal)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can update
        if ($currentUser->role->kode === 'SUPERADMIN') {
            abort(403, 'Super Admin tidak dapat mengedit jadwal.');
        }

        // Check ownership
        if ($bukaJadwal->cabang_id !== $currentUser->cabang_id) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        $cabangId = $currentUser->cabang_id;

        $validated = $request->validate([
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'tanggal' => 'required|date',
            'sesi_id' => [
                'required',
                'exists:sesi,id',
                function ($attribute, $value, $fail) use ($cabangId) {
                    $sesi = Sesi::find($value);
                    if ($sesi && $sesi->cabang_id !== $cabangId) {
                        $fail('Sesi tidak tersedia di cabang Anda.');
                    }
                },
            ],
            'jenisacara_id' => [
                'required',
                'exists:jenis_acara,id',
                function ($attribute, $value, $fail) use ($cabangId) {
                    $jenisAcara = JenisAcara::find($value);
                    if ($jenisAcara && $jenisAcara->cabang_id !== $cabangId) {
                        $fail('Jenis acara tidak tersedia di cabang Anda.');
                    }
                },
            ],
            'status_jadwal' => 'required|in:available,booked'
        ], [
            'hari.required' => 'Hari harus diisi',
            'tanggal.required' => 'Tanggal harus diisi',
            'sesi_id.required' => 'Sesi harus dipilih',
            'jenisacara_id.required' => 'Jenis acara harus dipilih',
            'status_jadwal.required' => 'Status jadwal harus dipilih'
        ]);

        // Check: Jika sudah ada booking, status TIDAK bisa diubah ke available
        $hasActiveBooking = $bukaJadwal->transaksiBooking()
            ->where('status_booking', 'active')
            ->exists();

        if ($hasActiveBooking && $validated['status_jadwal'] === 'available') {
            return redirect()->back()
                ->with('error', 'Status tidak dapat diubah ke Available karena jadwal memiliki booking aktif!')
                ->withInput();
        }

        // Auto-set hari dari tanggal
        $tanggal = Carbon::parse($validated['tanggal']);
        $hariMapping = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $validated['hari'] = $hariMapping[$tanggal->englishDayOfWeek] ?? $validated['hari'];

        // Check duplicate (exclude current record)
        $exists = BukaJadwal::where('tanggal', $validated['tanggal'])
            ->where('sesi_id', $validated['sesi_id'])
            ->where('cabang_id', $cabangId)
            ->where('id', '!=', $bukaJadwal->id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Jadwal dengan tanggal dan sesi yang sama sudah ada di cabang Anda!')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $bukaJadwal->update($validated);

            DB::commit();

            return redirect()->route('admin.transaksi.buka-jadwal.index')
                ->with('success', 'Buka jadwal berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating buka jadwal: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengupdate jadwal.')
                ->withInput();
        }
    }

    /**
     * Remove the specified buka jadwal
     * Only Admin Cabang can delete their own jadwal
     */
    public function destroy(BukaJadwal $bukaJadwal)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can delete
        if ($currentUser->role->kode === 'SUPERADMIN') {
            abort(403, 'Super Admin tidak dapat menghapus jadwal.');
        }

        // Check ownership
        if ($bukaJadwal->cabang_id !== $currentUser->cabang_id) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        // Check if jadwal is being used
        $bookingCount = $bukaJadwal->transaksiBooking()->count();

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
