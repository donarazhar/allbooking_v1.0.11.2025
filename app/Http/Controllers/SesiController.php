<?php

namespace App\Http\Controllers;

use App\Models\Sesi;
use Illuminate\Http\Request;

class SesiController extends Controller
{
    public function index()
    {
        $sesi = Sesi::orderBy('kode', 'asc')->get();
        return view('master.sesi.index', compact('sesi'));
    }

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

    public function update(Request $request, Sesi $sesi)
    {

        $validated = $request->validate([
            'kode' => 'required|max:10|unique:sesi,kode,' . $sesi->id,
            'nama' => 'required|max:255',
            'jam_mulai' => 'required|date_format:H:i:s',
            'jam_selesai' => 'required|date_format:H:i:s|after:jam_mulai',
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
        // After validation, remove seconds
        $validated['jam_mulai'] = substr($validated['jam_mulai'], 0, 5);
        $validated['jam_selesai'] = substr($validated['jam_selesai'], 0, 5);

        return redirect()->route('sesi.index')
            ->with('success', 'Data sesi berhasil diupdate!');
    }

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
