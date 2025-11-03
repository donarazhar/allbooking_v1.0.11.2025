<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Controller ini untuk mengelola data master user.
 */
class UserController extends Controller
{
    /**
     * Menampilkan halaman daftar semua user.
     */
    public function index()
    {
        $users = User::with('role')->orderBy('nama', 'asc')->get();
        return view('users.index', compact('users'));
    }

    /**
     * Menyimpan user baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|min:6',
            'role_id' => 'required|exists:roles,id',
            'status_users' => 'required|in:active,inactive'
        ], [
            'nama.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'role_id.required' => 'Role harus dipilih',
            'status_users.required' => 'Status harus dipilih'
        ]);

        // Enkripsi password sebelum disimpan
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Mengupdate data user di database.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'nama' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6', // Password tidak wajib diisi saat update
            'role_id' => 'required|exists:roles,id',
            'status_users' => 'required|in:active,inactive'
        ], [
            'nama.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.min' => 'Password minimal 6 karakter',
            'role_id.required' => 'Role harus dipilih',
            'status_users.required' => 'Status harus dipilih'
        ]);

        // Jika ada password baru, enkripsi. Jika tidak, jangan update password.
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate!');
    }

    /**
     * Menghapus user dari database.
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->route('users.index')
                ->with('success', 'User berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'User tidak dapat dihapus!');
        }
    }

    /**
     * Mengubah status user (active/inactive).
     */
    public function toggleStatus(User $user)
    {
        $newStatus = $user->status_users === 'active' ? 'inactive' : 'active';
        $user->update(['status_users' => $newStatus]);

        return redirect()->route('users.index')
            ->with('success', 'Status user berhasil diubah menjadi ' . $newStatus . '!');
    }
}
