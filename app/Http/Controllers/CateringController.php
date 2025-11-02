<?php

namespace App\Http\Controllers;

use App\Models\Catering;
use Illuminate\Http\Request;

class CateringController extends Controller
{
    public function index()
    {
        $catering = Catering::orderBy('nama', 'asc')->get();
        return view('master.catering.index', compact('catering'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|max:255',
            'email' => 'required|email|max:255',
            'no_hp' => 'required|max:20',
            'alamat' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'nama.required' => 'Nama catering harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'no_hp.required' => 'No HP harus diisi',
            'alamat.required' => 'Alamat harus diisi',
            'foto.image' => 'Foto harus berupa gambar',
            'foto.max' => 'Ukuran foto maksimal 2MB'
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/catering'), $filename);
            $validated['foto'] = $filename;
        }

        Catering::create($validated);

        return redirect()->route('catering.index')
            ->with('success', 'Data catering berhasil ditambahkan!');
    }

    public function update(Request $request, Catering $catering)
    {
        $validated = $request->validate([
            'nama' => 'required|max:255',
            'email' => 'required|email|max:255',
            'no_hp' => 'required|max:20',
            'alamat' => 'required',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'nama.required' => 'Nama catering harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'no_hp.required' => 'No HP harus diisi',
            'alamat.required' => 'Alamat harus diisi',
            'foto.image' => 'Foto harus berupa gambar',
            'foto.max' => 'Ukuran foto maksimal 2MB'
        ]);

        if ($request->hasFile('foto')) {
            if ($catering->foto && file_exists(public_path('uploads/catering/' . $catering->foto))) {
                unlink(public_path('uploads/catering/' . $catering->foto));
            }
            
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/catering'), $filename);
            $validated['foto'] = $filename;
        }

        $catering->update($validated);

        return redirect()->route('catering.index')
            ->with('success', 'Data catering berhasil diupdate!');
    }

    public function destroy(Catering $catering)
    {
        try {
            if ($catering->foto && file_exists(public_path('uploads/catering/' . $catering->foto))) {
                unlink(public_path('uploads/catering/' . $catering->foto));
            }
            
            $catering->delete();
            return redirect()->route('catering.index')
                ->with('success', 'Data catering berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('catering.index')
                ->with('error', 'Data catering tidak dapat dihapus karena masih digunakan!');
        }
    }
}
