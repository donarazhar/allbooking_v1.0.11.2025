<?php

namespace App\Http\Controllers;

use App\Models\Catering;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CateringController extends Controller
{
    public function index()
    {
        $catering = Catering::withCount('bookings')
            ->orderBy('nama', 'asc')
            ->get();
        return view('master.catering.index', compact('catering'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:catering,email|max:255',
            'no_hp' => 'required|string|min:10|max:13|regex:/^[0-9]+$/|unique:catering,no_hp',
            'alamat' => 'required|string|max:500',
            'password' => 'required|string|min:6',
            'keterangan' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'nama.required' => 'Nama catering harus diisi',
            'nama.max' => 'Nama maksimal 100 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'no_hp.required' => 'No HP harus diisi',
            'no_hp.unique' => 'No HP sudah terdaftar',
            'no_hp.min' => 'No HP minimal 10 digit',
            'no_hp.max' => 'No HP maksimal 13 digit',
            'no_hp.regex' => 'No HP hanya boleh berisi angka',
            'alamat.required' => 'Alamat harus diisi',
            'alamat.max' => 'Alamat maksimal 500 karakter',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'keterangan.max' => 'Keterangan maksimal 500 karakter',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'foto.max' => 'Ukuran foto maksimal 2MB'
        ]);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/catering'), $filename);
            $validated['foto'] = $filename;
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['nama'] = trim($validated['nama']);
        $validated['email'] = strtolower(trim($validated['email']));

        try {
            Catering::create($validated);
            return redirect()->route('admin.master.catering.index')
                ->with('success', 'Catering berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error creating catering: ' . $e->getMessage());
            
            if (isset($validated['foto']) && file_exists(public_path('uploads/catering/' . $validated['foto']))) {
                unlink(public_path('uploads/catering/' . $validated['foto']));
            }
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan catering.')
                ->withInput();
        }
    }

    public function update(Request $request, Catering $catering)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('catering')->ignore($catering->id),
            ],
            'no_hp' => [
                'required',
                'string',
                'min:10',
                'max:13',
                'regex:/^[0-9]+$/',
                Rule::unique('catering')->ignore($catering->id),
            ],
            'alamat' => 'required|string|max:500',
            'password' => 'nullable|string|min:6',
            'keterangan' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'nama.required' => 'Nama catering harus diisi',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email sudah digunakan',
            'no_hp.required' => 'No HP harus diisi',
            'no_hp.unique' => 'No HP sudah terdaftar',
            'no_hp.regex' => 'No HP hanya boleh berisi angka',
            'alamat.required' => 'Alamat harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'foto.image' => 'File harus berupa gambar',
            'foto.max' => 'Ukuran foto maksimal 2MB'
        ]);

        if ($request->hasFile('foto')) {
            if ($catering->foto && file_exists(public_path('uploads/catering/' . $catering->foto))) {
                unlink(public_path('uploads/catering/' . $catering->foto));
            }
            
            $file = $request->file('foto');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/catering'), $filename);
            $validated['foto'] = $filename;
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['nama'] = trim($validated['nama']);
        $validated['email'] = strtolower(trim($validated['email']));

        try {
            $catering->update($validated);
            return redirect()->route('admin.master.catering.index')
                ->with('success', 'Catering berhasil diupdate!');
        } catch (\Exception $e) {
            Log::error('Error updating catering: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengupdate catering.')
                ->withInput();
        }
    }

    public function destroy(Catering $catering)
    {
        $bookingCount = $catering->bookings()->count();
        
        if ($bookingCount > 0) {
            return redirect()->route('admin.master.catering.index')
                ->with('error', "Catering tidak dapat dihapus karena masih digunakan oleh {$bookingCount} booking!");
        }

        try {
            if ($catering->foto && file_exists(public_path('uploads/catering/' . $catering->foto))) {
                unlink(public_path('uploads/catering/' . $catering->foto));
            }

            $nama = $catering->nama;
            $catering->delete();
            
            return redirect()->route('admin.master.catering.index')
                ->with('success', "Catering '{$nama}' berhasil dihapus!");
        } catch (\Exception $e) {
            Log::error('Error deleting catering: ' . $e->getMessage());
            return redirect()->route('admin.master.catering.index')
                ->with('error', 'Terjadi kesalahan saat menghapus catering.');
        }
    }
}