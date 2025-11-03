<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

/**
 * Controller ini berfungsi untuk mengelola data master Role (peran pengguna).
 */
class RoleController extends Controller
{
    /**
     * Menampilkan halaman daftar semua role.
     */
    public function index()
    {
        $roles = Role::orderBy('nama', 'asc')->get();
        return view('master.role.index', compact('roles'));
    }

    /**
     * Menyimpan role baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|unique:roles,kode|max:10',
            'nama' => 'required|max:255',
            'keterangan' => 'nullable|string'
        ], [
            'kode.required' => 'Kode harus diisi',
            'kode.unique' => 'Kode sudah digunakan',
            'nama.required' => 'Nama role harus diisi'
        ]);

        Role::create($validated);

        return redirect()->route('role.index')
            ->with('success', 'Data role berhasil ditambahkan!');
    }

    /**
     * Mengupdate data role yang ada di database.
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'kode' => 'required|max:10|unique:roles,kode,' . $role->id,
            'nama' => 'required|max:255',
            'keterangan' => 'nullable|string'
        ], [
            'kode.required' => 'Kode harus diisi',
            'kode.unique' => 'Kode sudah digunakan',
            'nama.required' => 'Nama role harus diisi'
        ]);

        $role->update($validated);

        return redirect()->route('role.index')
            ->with('success', 'Data role berhasil diupdate!');
    }

    /**
     * Menghapus data role dari database.
     */
    public function destroy(Role $role)
    {
        try {
            $role->delete();
            return redirect()->route('role.index')
                ->with('success', 'Data role berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('role.index')
                ->with('error', 'Data role tidak dapat dihapus karena masih digunakan oleh user!');
        }
    }
}
