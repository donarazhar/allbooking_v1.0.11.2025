<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CabangController extends Controller
{
    /**
     * Display a listing of cabang
     */
    public function index(Request $request)
    {
        // Get all cabang
        $query = Cabang::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('kota', 'like', "%{$search}%")
                    ->orWhere('no_telp', 'like', "%{$search}%");
            });
        }

        $cabangList = $query->orderBy('nama')->get();

        return view('admin.master.cabang.index', compact('cabangList'));
    }

    /**
     * Store a newly created cabang
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:cabang,kode',
            'nama' => 'required|string|max:100',
            'alamat' => 'nullable|string|max:500',
            'kota' => 'nullable|string|max:100',
            'no_telp' => 'nullable|string|max:20',
        ], [
            'kode.required' => 'Kode cabang harus diisi',
            'kode.unique' => 'Kode cabang sudah terdaftar',
            'kode.max' => 'Kode cabang maksimal 50 karakter',
            'nama.required' => 'Nama cabang harus diisi',
            'nama.max' => 'Nama cabang maksimal 100 karakter',
            'alamat.max' => 'Alamat maksimal 500 karakter',
            'kota.max' => 'Kota maksimal 100 karakter',
            'no_telp.max' => 'No Telepon maksimal 20 karakter',
        ]);

        DB::beginTransaction();
        try {
            Cabang::create($validated);

            DB::commit();

            return redirect()->route('admin.master.cabang.index')
                ->with('success', 'Cabang berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating cabang: ' . $e->getMessage());

            return back()
                ->with('error', 'Terjadi kesalahan saat menambahkan cabang.')
                ->withInput();
        }
    }

    /**
     * Update the specified cabang
     */
    public function update(Request $request, Cabang $cabang)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:50|unique:cabang,kode,' . $cabang->id,
            'nama' => 'required|string|max:100',
            'alamat' => 'nullable|string|max:500',
            'kota' => 'nullable|string|max:100',
            'no_telp' => 'nullable|string|max:20',
        ], [
            'kode.required' => 'Kode cabang harus diisi',
            'kode.unique' => 'Kode cabang sudah terdaftar',
            'kode.max' => 'Kode cabang maksimal 50 karakter',
            'nama.required' => 'Nama cabang harus diisi',
            'nama.max' => 'Nama cabang maksimal 100 karakter',
            'alamat.max' => 'Alamat maksimal 500 karakter',
            'kota.max' => 'Kota maksimal 100 karakter',
            'no_telp.max' => 'No Telepon maksimal 20 karakter',
        ]);

        DB::beginTransaction();
        try {
            $cabang->update($validated);

            DB::commit();

            return redirect()->route('admin.master.cabang.index')
                ->with('success', 'Cabang berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating cabang: ' . $e->getMessage());

            return back()
                ->with('error', 'Terjadi kesalahan saat mengupdate cabang.')
                ->withInput();
        }
    }

    /**
     * Remove the specified cabang
     */
    public function destroy(Cabang $cabang)
    {
        DB::beginTransaction();
        try {
            // Check if cabang has related data
            $relatedData = [];

            // Check users
            $userCount = $cabang->users()->count();
            if ($userCount > 0) {
                $relatedData[] = "{$userCount} user";
            }

            // Check jadwal
            $jadwalCount = $cabang->bukaJadwal()->count();
            if ($jadwalCount > 0) {
                $relatedData[] = "{$jadwalCount} jadwal";
            }

            // Check jenis acara
            $jenisAcaraCount = $cabang->jenisAcara()->count();
            if ($jenisAcaraCount > 0) {
                $relatedData[] = "{$jenisAcaraCount} jenis acara";
            }

            // Check sesi
            $sesiCount = $cabang->sesi()->count();
            if ($sesiCount > 0) {
                $relatedData[] = "{$sesiCount} sesi";
            }

            // Check catering
            $cateringCount = $cabang->catering()->count();
            if ($cateringCount > 0) {
                $relatedData[] = "{$cateringCount} catering";
            }

            if (!empty($relatedData)) {
                $relatedDataStr = implode(', ', $relatedData);
                return redirect()->route('admin.master.cabang.index')
                    ->with('error', "Cabang tidak dapat dihapus karena masih memiliki: {$relatedDataStr}. Silakan hapus data terkait terlebih dahulu.");
            }

            $namaCabang = $cabang->nama;
            $cabang->delete();

            DB::commit();

            return redirect()->route('admin.master.cabang.index')
                ->with('success', "Cabang '{$namaCabang}' berhasil dihapus!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting cabang: ' . $e->getMessage());

            return redirect()->route('admin.master.cabang.index')
                ->with('error', 'Terjadi kesalahan saat menghapus cabang.');
        }
    }
}
