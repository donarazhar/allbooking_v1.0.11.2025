<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Controller untuk mengelola data master Role (peran pengguna).
 * 
 * Features:
 * - CRUD operations untuk role
 * - Protection untuk system roles (ADM, PIM, USR)
 * - Relationship checking sebelum delete
 * - Auto uppercase untuk kode role
 * - Comprehensive error handling
 */
class RoleController extends Controller
{
    /**
     * System roles yang tidak boleh dihapus atau diedit secara bebas
     */
    const PROTECTED_ROLES = ['ADM', 'PIM', 'USR'];

    /**
     * Menampilkan halaman daftar semua role.
     */
    public function index()
    {
        $roles = Role::withCount('users') // Hitung jumlah user per role
            ->orderBy('kode', 'asc')
            ->get();
            
        return view('master.role.index', compact('roles'));
    }

    /**
     * Menyimpan role baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'kode' => 'required|unique:roles,kode|max:10|alpha_dash',
            'nama' => 'required|max:100|string',
            'keterangan' => 'nullable|string|max:500'
        ], [
            'kode.required' => 'Kode role harus diisi',
            'kode.unique' => 'Kode role sudah digunakan',
            'kode.max' => 'Kode maksimal 10 karakter',
            'kode.alpha_dash' => 'Kode hanya boleh huruf, angka, dash, dan underscore',
            'nama.required' => 'Nama role harus diisi',
            'nama.max' => 'Nama maksimal 100 karakter',
            'keterangan.max' => 'Keterangan maksimal 500 karakter'
        ]);

        // Force uppercase untuk konsistensi
        $validated['kode'] = strtoupper(trim($validated['kode']));
        
        // Trim nama untuk remove extra spaces
        $validated['nama'] = trim($validated['nama']);

        try {
            Role::create($validated);

            return redirect()->route('admin.master.role.index')
                ->with('success', 'Role berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error creating role: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan role.')
                ->withInput();
        }
    }

    /**
     * Mengupdate data role yang ada di database.
     * 
     * Note: System roles (ADM, PIM, USR) hanya bisa update keterangan.
     */
    public function update(Request $request, Role $role)
    {
        // Check apakah ini system role
        $isSystemRole = in_array($role->kode, self::PROTECTED_ROLES);

        if ($isSystemRole) {
            // System role: Hanya boleh update keterangan
            $validated = $request->validate([
                'keterangan' => 'nullable|string|max:500'
            ], [
                'keterangan.max' => 'Keterangan maksimal 500 karakter'
            ]);
            
            $message = 'Keterangan role sistem berhasil diupdate!';
        } else {
            // Custom role: Boleh update semua field
            $validated = $request->validate([
                'kode' => 'required|max:10|alpha_dash|unique:roles,kode,' . $role->id,
                'nama' => 'required|max:100|string',
                'keterangan' => 'nullable|string|max:500'
            ], [
                'kode.required' => 'Kode role harus diisi',
                'kode.unique' => 'Kode role sudah digunakan',
                'kode.max' => 'Kode maksimal 10 karakter',
                'kode.alpha_dash' => 'Kode hanya boleh huruf, angka, dash, dan underscore',
                'nama.required' => 'Nama role harus diisi',
                'nama.max' => 'Nama maksimal 100 karakter',
                'keterangan.max' => 'Keterangan maksimal 500 karakter'
            ]);

            // Force uppercase untuk kode
            $validated['kode'] = strtoupper(trim($validated['kode']));
            $validated['nama'] = trim($validated['nama']);
            
            $message = 'Role berhasil diupdate!';
        }

        try {
            $role->update($validated);

            return redirect()->route('admin.master.role.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error updating role: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengupdate role.')
                ->withInput();
        }
    }

    /**
     * Menghapus data role dari database.
     * 
     * Protection:
     * - Tidak bisa hapus system roles (ADM, PIM, USR)
     * - Tidak bisa hapus role yang masih digunakan oleh user
     */
    public function destroy(Role $role)
    {
        // 1. Protect system roles
        if (in_array($role->kode, self::PROTECTED_ROLES)) {
            return redirect()->route('admin.master.role.index')
                ->with('error', 'Role sistem tidak dapat dihapus!');
        }

        // 2. Check if role is being used by any user
        $userCount = $role->users()->count();
        
        if ($userCount > 0) {
            return redirect()->route('admin.master.role.index')
                ->with('error', "Role tidak dapat dihapus karena masih digunakan oleh {$userCount} user!");
        }

        // 3. Safe to delete
        try {
            $roleName = $role->nama;
            $role->delete();

            return redirect()->route('admin.master.role.index')
                ->with('success', "Role '{$roleName}' berhasil dihapus!");
        } catch (\Exception $e) {
            Log::error('Error deleting role: ' . $e->getMessage(), [
                'role_id' => $role->id,
                'role_kode' => $role->kode
            ]);

            return redirect()->route('admin.master.role.index')
                ->with('error', 'Terjadi kesalahan saat menghapus role. Silakan coba lagi.');
        }
    }

    /**
     * Check if a role is a system role
     * 
     * @param string $kode
     * @return bool
     */
    public static function isSystemRole($kode)
    {
        return in_array(strtoupper($kode), self::PROTECTED_ROLES);
    }

    /**
     * Get list of system roles
     * 
     * @return array
     */
    public static function getSystemRoles()
    {
        return self::PROTECTED_ROLES;
    }
}