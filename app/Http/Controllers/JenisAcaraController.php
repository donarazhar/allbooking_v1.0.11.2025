<?php

namespace App\Http\Controllers;

use App\Models\JenisAcara;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class JenisAcaraController extends Controller
{
    /**
     * Display a listing of jenis acara
     * 
     * Super Admin: Lihat SEMUA jenis acara dari semua cabang (readonly)
     * Admin Cabang: Lihat jenis acara cabangnya saja (CRUD)
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->role->kode === 'SUPERADMIN';

        // Base query dengan relationship
        $query = JenisAcara::with('cabang')->withCount('bukaJadwal');

        // =====================
        // LOGIC: Filter berdasarkan role
        // =====================
        if ($isSuperAdmin) {
            // Super Admin: Lihat SEMUA jenis acara dari semua cabang

            // Filter by cabang jika ada request
            if ($request->filled('cabang_id')) {
                $query->where('jenis_acara.cabang_id', $request->cabang_id);
            }
        } else {
            // Admin Cabang: Hanya jenis acara cabangnya
            $cabangId = $currentUser->cabang_id;
            $query->where('jenis_acara.cabang_id', $cabangId);
        }

        // Search filter (untuk admin cabang)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('jenis_acara.nama', 'like', "%{$search}%")
                    ->orWhere('jenis_acara.kode', 'like', "%{$search}%");
            });
        }

        // Get jenis acara data
        $jenisAcara = $query->orderBy('jenis_acara.kode', 'asc')->get();

        // Get cabang list untuk filter (Super Admin)
        $cabangList = Cabang::orderBy('nama')->get();

        // Get current cabang info (Admin Cabang)
        $cabangInfo = !$isSuperAdmin ? Cabang::find($currentUser->cabang_id) : null;

        return view('admin.master.jenis-acara.index', compact(
            'jenisAcara',
            'cabangList',
            'cabangInfo',
            'isSuperAdmin'
        ));
    }

    /**
     * Store a newly created jenis acara
     * Only Admin Cabang can create
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can create
        if ($currentUser->role->kode === 'SUPERADMIN') {
            abort(403, 'Super Admin tidak dapat menambah jenis acara. Fitur ini khusus untuk Admin Cabang.');
        }

        $cabangId = $currentUser->cabang_id;

        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                // Unique per cabang
                function ($attribute, $value, $fail) use ($cabangId) {
                    $exists = JenisAcara::where('kode', strtoupper($value))
                        ->where('cabang_id', $cabangId)
                        ->exists();
                    if ($exists) {
                        $fail('Kode jenis acara sudah digunakan di cabang ini.');
                    }
                },
            ],
            'nama' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0|max:999999999',
            'keterangan' => 'nullable|string|max:500',
            'status_jenis_acara' => 'required|in:active,inactive'
        ], [
            'kode.required' => 'Kode harus diisi',
            'kode.alpha_dash' => 'Kode hanya boleh huruf, angka, dash, dan underscore',
            'nama.required' => 'Nama jenis acara harus diisi',
            'nama.max' => 'Nama maksimal 100 karakter',
            'harga.required' => 'Harga harus diisi',
            'harga.min' => 'Harga minimal 0',
            'harga.max' => 'Harga maksimal 999.999.999',
            'status_jenis_acara.required' => 'Status harus dipilih',
            'keterangan.max' => 'Keterangan maksimal 500 karakter'
        ]);

        DB::beginTransaction();
        try {
            $validated['kode'] = strtoupper(trim($validated['kode']));
            $validated['nama'] = trim($validated['nama']);
            $validated['cabang_id'] = $cabangId; // Set cabang_id

            JenisAcara::create($validated);

            DB::commit();

            return redirect()->route('admin.master.jenis-acara.index')
                ->with('success', 'Jenis Acara berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating jenis acara: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan jenis acara.')
                ->withInput();
        }
    }

    /**
     * Update the specified jenis acara
     * Only Admin Cabang can update their own jenis acara
     */
    public function update(Request $request, JenisAcara $jenisAcara)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can update
        if ($currentUser->role->kode === 'SUPERADMIN') {
            abort(403, 'Super Admin tidak dapat mengedit jenis acara.');
        }

        // Check ownership
        if ($jenisAcara->cabang_id !== $currentUser->cabang_id) {
            abort(403, 'Anda tidak memiliki akses ke jenis acara ini.');
        }

        $validated = $request->validate([
            'kode' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                // Unique per cabang, exclude current jenis acara
                function ($attribute, $value, $fail) use ($jenisAcara, $currentUser) {
                    $exists = JenisAcara::where('kode', strtoupper($value))
                        ->where('cabang_id', $currentUser->cabang_id)
                        ->where('id', '!=', $jenisAcara->id)
                        ->exists();
                    if ($exists) {
                        $fail('Kode jenis acara sudah digunakan di cabang ini.');
                    }
                },
            ],
            'nama' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0|max:999999999',
            'keterangan' => 'nullable|string|max:500',
            'status_jenis_acara' => 'required|in:active,inactive'
        ], [
            'kode.required' => 'Kode harus diisi',
            'kode.alpha_dash' => 'Kode hanya boleh huruf, angka, dash, dan underscore',
            'nama.required' => 'Nama jenis acara harus diisi',
            'harga.required' => 'Harga harus diisi',
            'harga.min' => 'Harga minimal 0',
            'status_jenis_acara.required' => 'Status harus dipilih'
        ]);

        DB::beginTransaction();
        try {
            $validated['kode'] = strtoupper(trim($validated['kode']));
            $validated['nama'] = trim($validated['nama']);

            $jenisAcara->update($validated);

            DB::commit();

            return redirect()->route('admin.master.jenis-acara.index')
                ->with('success', 'Jenis Acara berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating jenis acara: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengupdate jenis acara.')
                ->withInput();
        }
    }

    /**
     * Remove the specified jenis acara
     * Only Admin Cabang can delete their own jenis acara
     */
    public function destroy(JenisAcara $jenisAcara)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can delete
        if ($currentUser->role->kode === 'SUPERADMIN') {
            abort(403, 'Super Admin tidak dapat menghapus jenis acara.');
        }

        // Check ownership
        if ($jenisAcara->cabang_id !== $currentUser->cabang_id) {
            abort(403, 'Anda tidak memiliki akses ke jenis acara ini.');
        }

        // Check if jenis acara is being used
        $jadwalCount = $jenisAcara->bukaJadwal()->count();

        if ($jadwalCount > 0) {
            return redirect()->route('admin.master.jenis-acara.index')
                ->with('error', "Jenis Acara tidak dapat dihapus karena masih digunakan oleh {$jadwalCount} jadwal!");
        }

        try {
            $nama = $jenisAcara->nama;
            $jenisAcara->delete();

            return redirect()->route('admin.master.jenis-acara.index')
                ->with('success', "Jenis Acara '{$nama}' berhasil dihapus!");
        } catch (\Exception $e) {
            Log::error('Error deleting jenis acara: ' . $e->getMessage());
            return redirect()->route('admin.master.jenis-acara.index')
                ->with('error', 'Terjadi kesalahan saat menghapus jenis acara.');
        }
    }

    /**
     * Toggle status of jenis acara
     * Admin Cabang can toggle their own jenis acara
     */
    public function toggleStatus(JenisAcara $jenisAcara)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can toggle
        if ($currentUser->role->kode === 'SUPERADMIN') {
            abort(403, 'Super Admin tidak dapat mengubah status jenis acara.');
        }

        // Check ownership
        if ($jenisAcara->cabang_id !== $currentUser->cabang_id) {
            abort(403, 'Anda tidak memiliki akses ke jenis acara ini.');
        }

        $newStatus = $jenisAcara->status_jenis_acara === 'active' ? 'inactive' : 'active';
        $jenisAcara->update(['status_jenis_acara' => $newStatus]);

        $statusText = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.master.jenis-acara.index')
            ->with('success', "Jenis Acara '{$jenisAcara->nama}' berhasil {$statusText}!");
    }
}
