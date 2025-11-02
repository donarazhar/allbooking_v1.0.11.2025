<?php

namespace App\Http\Controllers;

use App\Models\JenisAcara;
use Illuminate\Http\Request;

class JenisAcaraController extends Controller
{
    public function index()
    {
        $jenisAcara = JenisAcara::orderBy('kode', 'asc')->get();
        return view('master.jenis-acara.index', compact('jenisAcara'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|unique:jenis_acara,kode|max:10',
            'nama' => 'required|max:255',
            'keterangan' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'status_jenis_acara' => 'required|in:active,inactive'
        ], [
            'kode.required' => 'Kode harus diisi',
            'kode.unique' => 'Kode sudah digunakan',
            'nama.required' => 'Nama jenis acara harus diisi',
            'harga.required' => 'Harga harus diisi',
            'status_jenis_acara.required' => 'Status harus dipilih'
        ]);

        JenisAcara::create($validated);

        return redirect()->route('jenis-acara.index')
            ->with('success', 'Data jenis acara berhasil ditambahkan!');
    }

    public function update(Request $request, JenisAcara $jenisAcara)
    {
        $validated = $request->validate([
            'kode' => 'required|max:10|unique:jenis_acara,kode,' . $jenisAcara->id,
            'nama' => 'required|max:255',
            'keterangan' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'status_jenis_acara' => 'required|in:active,inactive'
        ], [
            'kode.required' => 'Kode harus diisi',
            'kode.unique' => 'Kode sudah digunakan',
            'nama.required' => 'Nama jenis acara harus diisi',
            'harga.required' => 'Harga harus diisi',
            'status_jenis_acara.required' => 'Status harus dipilih'
        ]);

        $jenisAcara->update($validated);

        return redirect()->route('jenis-acara.index')
            ->with('success', 'Data jenis acara berhasil diupdate!');
    }

    public function destroy(JenisAcara $jenisAcara)
    {
        try {
            $jenisAcara->delete();
            return redirect()->route('jenis-acara.index')
                ->with('success', 'Data jenis acara berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('jenis-acara.index')
                ->with('error', 'Data jenis acara tidak dapat dihapus karena masih digunakan!');
        }
    }

    public function toggleStatus(JenisAcara $jenisAcara)
    {
        $newStatus = $jenisAcara->status_jenis_acara === 'active' ? 'inactive' : 'active';
        $jenisAcara->update(['status_jenis_acara' => $newStatus]);

        return redirect()->route('jenis-acara.index')
            ->with('success', 'Status jenis acara berhasil diubah menjadi ' . $newStatus . '!');
    }
}
