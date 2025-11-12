<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    const PROTECTED_USERS = ['admin@aula.com']; // Protected admin email
    
    public function index()
    {
        $users = User::with('role')
            ->withCount('bookings') // Count bookings
            ->orderBy('nama', 'asc')
            ->get();
            
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email|max:255',
            'no_hp' => 'required|string|min:10|max:13|regex:/^[0-9]+$/|unique:users,no_hp',
            'alamat' => 'nullable|string|max:500',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'status_users' => 'required|in:active,inactive',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ], [
            'nama.required' => 'Nama harus diisi',
            'nama.max' => 'Nama maksimal 100 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'no_hp.required' => 'Nomor HP harus diisi',
            'no_hp.unique' => 'Nomor HP sudah terdaftar',
            'no_hp.min' => 'Nomor HP minimal 10 digit',
            'no_hp.max' => 'Nomor HP maksimal 13 digit',
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'role_id.required' => 'Role harus dipilih',
            'status_users.required' => 'Status harus dipilih',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'foto.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        // Handle foto upload
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/profile'), $filename);
            $validated['foto'] = $filename;
        }

        // Hash password
        $validated['password'] = Hash::make($validated['password']);
        
        // Trim data
        $validated['nama'] = trim($validated['nama']);
        $validated['email'] = strtolower(trim($validated['email']));

        try {
            User::create($validated);
            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil ditambahkan!');
        } catch (\Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            
            // Delete uploaded foto if exists
            if (isset($validated['foto']) && file_exists(public_path('uploads/profile/' . $validated['foto']))) {
                unlink(public_path('uploads/profile/' . $validated['foto']));
            }
            
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan user.')
                ->withInput();
        }
    }

    public function update(Request $request, User $user)
    {
        // Check if protected user
        if (in_array($user->email, self::PROTECTED_USERS)) {
            // Protected user: Cannot change email & role
            $validated = $request->validate([
                'nama' => 'required|string|max:100',
                'no_hp' => [
                    'required',
                    'string',
                    'min:10',
                    'max:13',
                    'regex:/^[0-9]+$/',
                    Rule::unique('users')->ignore($user->id),
                ],
                'alamat' => 'nullable|string|max:500',
                'password' => 'nullable|string|min:6',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ], [
                'nama.required' => 'Nama harus diisi',
                'no_hp.required' => 'Nomor HP harus diisi',
                'no_hp.unique' => 'Nomor HP sudah terdaftar',
                'no_hp.regex' => 'Nomor HP hanya boleh berisi angka',
                'password.min' => 'Password minimal 6 karakter',
            ]);
        } else {
            // Regular user: Can change all fields
            $validated = $request->validate([
                'nama' => 'required|string|max:100',
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
                'no_hp' => [
                    'required',
                    'string',
                    'min:10',
                    'max:13',
                    'regex:/^[0-9]+$/',
                    Rule::unique('users')->ignore($user->id),
                ],
                'alamat' => 'nullable|string|max:500',
                'password' => 'nullable|string|min:6',
                'role_id' => 'required|exists:roles,id',
                'status_users' => 'required|in:active,inactive',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ], [
                'nama.required' => 'Nama harus diisi',
                'email.required' => 'Email harus diisi',
                'email.unique' => 'Email sudah digunakan',
                'no_hp.required' => 'Nomor HP harus diisi',
                'no_hp.unique' => 'Nomor HP sudah terdaftar',
                'no_hp.regex' => 'Nomor HP hanya boleh berisi angka',
                'role_id.required' => 'Role harus dipilih',
                'password.min' => 'Password minimal 6 karakter',
            ]);
            
            $validated['email'] = strtolower(trim($validated['email']));
        }

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Delete old foto
            if ($user->foto && file_exists(public_path('uploads/profile/' . $user->foto))) {
                unlink(public_path('uploads/profile/' . $user->foto));
            }
            
            $file = $request->file('foto');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/profile'), $filename);
            $validated['foto'] = $filename;
        }

        // Handle password update
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        
        // Trim nama
        $validated['nama'] = trim($validated['nama']);

        try {
            $user->update($validated);
            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil diupdate!');
        } catch (\Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengupdate user.')
                ->withInput();
        }
    }

    public function destroy(User $user)
    {
        // 1. Protect main admin
        if (in_array($user->email, self::PROTECTED_USERS)) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User admin utama tidak dapat dihapus!');
        }

        // 2. Check if user has bookings
        $bookingCount = $user->bookings()->count();
        
        if ($bookingCount > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', "User tidak dapat dihapus karena memiliki {$bookingCount} transaksi booking!");
        }

        try {
            // Delete foto if exists
            if ($user->foto && file_exists(public_path('uploads/profile/' . $user->foto))) {
                unlink(public_path('uploads/profile/' . $user->foto));
            }
            
            $userName = $user->nama;
            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', "User '{$userName}' berhasil dihapus!");
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return redirect()->route('admin.users.index')
                ->with('error', 'Terjadi kesalahan saat menghapus user.');
        }
    }

    public function toggleStatus(User $user)
    {
        // Protect main admin
        if (in_array($user->email, self::PROTECTED_USERS)) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Status user admin utama tidak dapat diubah!');
        }

        $newStatus = $user->status_users === 'active' ? 'inactive' : 'active';
        $user->update(['status_users' => $newStatus]);

        $statusText = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->nama}' berhasil {$statusText}!");
    }
}