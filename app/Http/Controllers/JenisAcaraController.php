<?php

namespace App\Http\Controllers;

use App\Models\JenisAcara;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JenisAcaraController extends Controller
{
    public function index()
    {
        $jenisAcara = JenisAcara::withCount('bukaJadwal')
            ->orderBy('kode', 'asc')
            ->get();
        return view('master.jenis-acara.index', compact('jenisAcara'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|unique:jenis_acara,kode|max:10|alpha_dash',
            'nama' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0|max:999999999',
            'keterangan' => 'nullable|string|max:500',
            'status_jenis_acara' => 'required|in:active,inactive'
        ], [
            'kode.required' => 'Kode harus diisi',
            'kode.unique' => 'Kode sudah digunakan',
            'kode.alpha_dash' => 'Kode hanya boleh huruf, angka, dash, dan underscore',
            'nama.required' => 'Nama jenis acara harus diisi',
            'nama.max' => 'Nama maksimal 100 karakter',
            'harga.required' => 'Harga harus diisi',
            'harga.min' => 'Harga minimal 0',
            'harga.max' => 'Harga maksimal 999.999.999',
            'status_jenis_acara.required' => 'Status harus dipilih',
            'keterangan.max' => 'Keterangan maksimal 500 karakter'
        ]);

        $validated['kode'] = strtoupper(trim($validated['kode']));
        $validated['nama'] = trim($validated['nama']);

        try {
            JenisAcara::create($validated);
            return redirect()->route('admin.master.jenis-acara.index')
                ->with('success', 'Jenis Acara berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error creating jenis acara: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan jenis acara.')
                ->withInput();
        }
    }

    public function update(Request $request, JenisAcara $jenisAcara)
    {
        $validated = $request->validate([
            'kode' => 'required|string|max:10|alpha_dash|unique:jenis_acara,kode,' . $jenisAcara->id,
            'nama' => 'required|string|max:100',
            'harga' => 'required|numeric|min:0|max:999999999',
            'keterangan' => 'nullable|string|max:500',
            'status_jenis_acara' => 'required|in:active,inactive'
        ], [
            'kode.required' => 'Kode harus diisi',
            'kode.unique' => 'Kode sudah digunakan',
            'kode.alpha_dash' => 'Kode hanya boleh huruf, angka, dash, dan underscore',
            'nama.required' => 'Nama jenis acara harus diisi',
            'harga.required' => 'Harga harus diisi',
            'harga.min' => 'Harga minimal 0',
            'status_jenis_acara.required' => 'Status harus dipilih'
        ]);

        $validated['kode'] = strtoupper(trim($validated['kode']));
        $validated['nama'] = trim($validated['nama']);

        try {
            $jenisAcara->update($validated);
            return redirect()->route('admin.master.jenis-acara.index')
                ->with('success', 'Jenis Acara berhasil diupdate!');
        } catch (\Exception $e) {
            Log::error('Error updating jenis acara: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengupdate jenis acara.')
                ->withInput();
        }
    }

    public function destroy(JenisAcara $jenisAcara)
    {
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

    public function toggleStatus(JenisAcara $jenisAcara)
    {
        $newStatus = $jenisAcara->status_jenis_acara === 'active' ? 'inactive' : 'active';
        $jenisAcara->update(['status_jenis_acara' => $newStatus]);

        $statusText = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.master.jenis-acara.index')
            ->with('success', "Jenis Acara '{$jenisAcara->nama}' berhasil {$statusText}!");
    }
}