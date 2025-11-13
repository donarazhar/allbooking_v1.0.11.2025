<?php

namespace App\Http\Controllers;

use App\Models\Sesi;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SesiController extends Controller
{
    /**
     * Display a listing of sesi
     * 
     * Super Admin: Lihat SEMUA sesi dari semua cabang (readonly)
     * Admin Cabang: Lihat sesi cabangnya saja (CRUD)
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->role->kode === 'SUPERADMIN';

        // Base query dengan relationship
        $query = Sesi::with('cabang')->withCount('bukaJadwal');

        // =====================
        // LOGIC: Filter berdasarkan role
        // =====================
        if ($isSuperAdmin) {
            // Super Admin: Lihat SEMUA sesi dari semua cabang

            // Filter by cabang jika ada request
            if ($request->filled('cabang_id')) {
                $query->where('sesi.cabang_id', $request->cabang_id);
            }
        } else {
            // Admin Cabang: Hanya sesi cabangnya
            $cabangId = $currentUser->cabang_id;
            $query->where('sesi.cabang_id', $cabangId);
        }

        // Search filter (untuk admin cabang)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sesi.nama', 'like', "%{$search}%")
                    ->orWhere('sesi.kode', 'like', "%{$search}%");
            });
        }

        // Get sesi data
        $sesi = $query->orderBy('sesi.jam_mulai', 'asc')->get();

        // Get cabang list untuk filter (Super Admin)
        $cabangList = Cabang::orderBy('nama')->get();

        // Get current cabang info (Admin Cabang)
        $cabangInfo = !$isSuperAdmin ? Cabang::find($currentUser->cabang_id) : null;

        return view('admin.master.sesi.index', compact(
            'sesi',
            'cabangList',
            'cabangInfo',
            'isSuperAdmin'
        ));
    }

    /**
     * Store a newly created sesi
     * Only Admin Cabang can create
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can create
        if ($currentUser->role->kode === 'SUPERADMIN') {
            abort(403, 'Super Admin tidak dapat menambah sesi. Fitur ini khusus untuk Admin Cabang.');
        }

        $cabangId = $currentUser->cabang_id;

        $validated = $request->validate([
            'kode' => [
                'required',
                'max:50',
                'alpha_dash',
                // Unique per cabang
                function ($attribute, $value, $fail) use ($cabangId) {
                    $exists = Sesi::where('kode', strtoupper($value))
                        ->where('cabang_id', $cabangId)
                        ->exists();
                    if ($exists) {
                        $fail('Kode sesi sudah digunakan di cabang ini.');
                    }
                },
            ],
            'nama' => 'required|string|max:100',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'nullable|string|max:500'
        ], [
            'kode.required' => 'Kode harus diisi',
            'kode.max' => 'Kode maksimal 50 karakter',
            'kode.alpha_dash' => 'Kode hanya boleh huruf, angka, dash, dan underscore',
            'nama.required' => 'Nama sesi harus diisi',
            'nama.max' => 'Nama maksimal 100 karakter',
            'jam_mulai.required' => 'Jam mulai harus diisi',
            'jam_mulai.date_format' => 'Format jam mulai tidak valid (HH:MM)',
            'jam_selesai.required' => 'Jam selesai harus diisi',
            'jam_selesai.date_format' => 'Format jam selesai tidak valid (HH:MM)',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai',
            'keterangan.max' => 'Keterangan maksimal 500 karakter'
        ]);

        DB::beginTransaction();
        try {
            // Force uppercase kode
            $validated['kode'] = strtoupper(trim($validated['kode']));
            $validated['nama'] = trim($validated['nama']);
            $validated['cabang_id'] = $cabangId; // Set cabang_id

            Sesi::create($validated);

            DB::commit();

            return redirect()->route('admin.master.sesi.index')
                ->with('success', 'Sesi berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating sesi: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan sesi.')
                ->withInput();
        }
    }

    /**
     * Update the specified sesi
     * Only Admin Cabang can update their own sesi
     */
    public function update(Request $request, Sesi $sesi)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can update
        if ($currentUser->role->kode === 'SUPERADMIN') {
            abort(403, 'Super Admin tidak dapat mengedit sesi.');
        }

        // Check ownership
        if ($sesi->cabang_id !== $currentUser->cabang_id) {
            abort(403, 'Anda tidak memiliki akses ke sesi ini.');
        }

        $validated = $request->validate([
            'kode' => [
                'required',
                'max:50',
                'alpha_dash',
                // Unique per cabang, exclude current sesi
                function ($attribute, $value, $fail) use ($sesi, $currentUser) {
                    $exists = Sesi::where('kode', strtoupper($value))
                        ->where('cabang_id', $currentUser->cabang_id)
                        ->where('id', '!=', $sesi->id)
                        ->exists();
                    if ($exists) {
                        $fail('Kode sesi sudah digunakan di cabang ini.');
                    }
                },
            ],
            'nama' => 'required|string|max:100',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'nullable|string|max:500'
        ], [
            'kode.required' => 'Kode harus diisi',
            'kode.alpha_dash' => 'Kode hanya boleh huruf, angka, dash, dan underscore',
            'nama.required' => 'Nama sesi harus diisi',
            'jam_mulai.required' => 'Jam mulai harus diisi',
            'jam_selesai.required' => 'Jam selesai harus diisi',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai'
        ]);

        DB::beginTransaction();
        try {
            // Force uppercase kode
            $validated['kode'] = strtoupper(trim($validated['kode']));
            $validated['nama'] = trim($validated['nama']);

            $sesi->update($validated);

            DB::commit();

            return redirect()->route('admin.master.sesi.index')
                ->with('success', 'Sesi berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating sesi: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengupdate sesi.')
                ->withInput();
        }
    }

    /**
     * Remove the specified sesi
     * Only Admin Cabang can delete their own sesi
     */
    public function destroy(Sesi $sesi)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can delete
        if ($currentUser->role->kode === 'SUPERADMIN') {
            abort(403, 'Super Admin tidak dapat menghapus sesi.');
        }

        // Check ownership
        if ($sesi->cabang_id !== $currentUser->cabang_id) {
            abort(403, 'Anda tidak memiliki akses ke sesi ini.');
        }

        // Check if sesi is being used
        $jadwalCount = $sesi->bukaJadwal()->count();

        if ($jadwalCount > 0) {
            return redirect()->route('admin.master.sesi.index')
                ->with('error', "Sesi tidak dapat dihapus karena masih digunakan oleh {$jadwalCount} jadwal!");
        }

        try {
            $sesiNama = $sesi->nama;
            $sesi->delete();

            return redirect()->route('admin.master.sesi.index')
                ->with('success', "Sesi '{$sesiNama}' berhasil dihapus!");
        } catch (\Exception $e) {
            Log::error('Error deleting sesi: ' . $e->getMessage());
            return redirect()->route('admin.master.sesi.index')
                ->with('error', 'Terjadi kesalahan saat menghapus sesi.');
        }
    }
}
