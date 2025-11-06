<?php

namespace App\Http\Controllers;

use App\Models\Sesi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SesiController extends Controller
{
    public function index()
    {
        $sesi = Sesi::withCount('bukaJadwal')
            ->orderBy('jam_mulai', 'asc')
            ->get();
        return view('master.sesi.index', compact('sesi'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|unique:sesi,kode|max:10|alpha_dash',
            'nama' => 'required|string|max:100',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'nullable|string|max:500'
        ], [
            'kode.required' => 'Kode harus diisi',
            'kode.unique' => 'Kode sudah digunakan',
            'kode.max' => 'Kode maksimal 10 karakter',
            'kode.alpha_dash' => 'Kode hanya boleh huruf, angka, dash, dan underscore',
            'nama.required' => 'Nama sesi harus diisi',
            'nama.max' => 'Nama maksimal 100 karakter',
            'jam_mulai.required' => 'Jam mulai harus diisi',
            'jam_mulai.date_format' => 'Format jam mulai tidak valid',
            'jam_selesai.required' => 'Jam selesai harus diisi',
            'jam_selesai.date_format' => 'Format jam selesai tidak valid',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai',
            'keterangan.max' => 'Keterangan maksimal 500 karakter'
        ]);

        // Force uppercase kode
        $validated['kode'] = strtoupper(trim($validated['kode']));
        $validated['nama'] = trim($validated['nama']);

        try {
            Sesi::create($validated);
            return redirect()->route('admin.master.sesi.index')
                ->with('success', 'Sesi berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error creating sesi: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan sesi.')
                ->withInput();
        }
    }

    public function update(Request $request, Sesi $sesi)
    {
        $validated = $request->validate([
            'kode' => 'required|max:10|alpha_dash|unique:sesi,kode,' . $sesi->id,
            'nama' => 'required|string|max:100',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'nullable|string|max:500'
        ], [
            'kode.required' => 'Kode harus diisi',
            'kode.unique' => 'Kode sudah digunakan',
            'kode.alpha_dash' => 'Kode hanya boleh huruf, angka, dash, dan underscore',
            'nama.required' => 'Nama sesi harus diisi',
            'jam_mulai.required' => 'Jam mulai harus diisi',
            'jam_selesai.required' => 'Jam selesai harus diisi',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai'
        ]);

        // Force uppercase kode
        $validated['kode'] = strtoupper(trim($validated['kode']));
        $validated['nama'] = trim($validated['nama']);

        try {
            $sesi->update($validated);
            return redirect()->route('admin.master.sesi.index')
                ->with('success', 'Sesi berhasil diupdate!');
        } catch (\Exception $e) {
            Log::error('Error updating sesi: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengupdate sesi.')
                ->withInput();
        }
    }

    public function destroy(Sesi $sesi)
    {
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