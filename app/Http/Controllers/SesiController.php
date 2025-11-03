<?php

namespace App\Http\Controllers;

use App\Models\Sesi;
use Illuminate\Http\Request;

/**
 * Controller untuk mengelola semua logika terkait data master sesi.
 */
class SesiController extends Controller
{
    /**
     * Menampilkan halaman utama yang berisi daftar semua sesi.
     */
    public function index()
    {
        $sesi = Sesi::orderBy('kode', 'asc')->get();
        return view('master.sesi.index', compact('sesi'));
    }

    /**
     * Menyimpan data sesi baru yang dikirim dari form.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|unique:sesi,kode|max:10',
            'nama' => 'required|max:255',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'nullable|string'
        ], [
            'kode.required' => 'Kode harus diisi',
            'kode.unique' => 'Kode sudah digunakan',
            'nama.required' => 'Nama sesi harus diisi',
            'jam_mulai.required' => 'Jam mulai harus diisi',
            'jam_selesai.required' => 'Jam selesai harus diisi',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai'
        ]);

        Sesi::create($validated);
      
        return redirect()->route('sesi.index')
            ->with('success', 'Data sesi berhasil ditambahkan!');
    }

    /**
     * Mengupdate data sesi yang sudah ada berdasarkan ID.
     */
    public function update(Request $request, Sesi $sesi)
    {

        $validated = $request->validate([
            'kode' => 'required|max:10|unique:sesi,kode,' . $sesi->id,
            'nama' => 'required|max:255',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'keterangan' => 'nullable|string'
        ], [
            'kode.required' => 'Kode harus diisi',
            'kode.unique' => 'Kode sudah digunakan',
            'nama.required' => 'Nama sesi harus diisi',
            'jam_mulai.required' => 'Jam mulai harus diisi',
            'jam_selesai.required' => 'Jam selesai harus diisi',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai'
        ]);

        $sesi->update($validated);
        
        return redirect()->route('sesi.index')
            ->with('success', 'Data sesi berhasil diupdate!');
    }

    /**
     * Menghapus data sesi dari database berdasarkan ID.
     */
    public function destroy(Sesi $sesi)
    {
        try {
            $sesi->delete();
            return redirect()->route('sesi.index')
                ->with('success', 'Data sesi berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('sesi.index')
                ->with('error', 'Data sesi tidak dapat dihapus karena masih digunakan!');
        }
    }
}
