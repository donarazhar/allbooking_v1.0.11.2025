<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role && $user->role->nama === 'User') {
                return redirect()->route('user.dashboard');
            }
            return redirect('/dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi'
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Check if user is active
            if ($user->status_users !== 'active') {
                Auth::logout();
                return back()->with('error', 'Akun Anda belum disetujui. Silakan hubungi administrator.');
            }

            // Redirect based on role
            if ($user->role && $user->role->nama === 'User') {
                return redirect()->route('user.dashboard')->with('success', 'Selamat datang, ' . $user->nama . '!');
            }
            
            return redirect()->intended('/dashboard')->with('success', 'Selamat datang, ' . $user->nama . '!');
        }

        return back()->with('error', 'Email atau password salah!')->withInput();
    }

    public function showRegister()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role && $user->role->nama === 'User') {
                return redirect()->route('user.dashboard');
            }
            return redirect('/dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|min:6|confirmed',
        ], [
            'nama.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok'
        ]);

        // Get default "User" role
        $userRole = Role::where('nama', 'User')->first();
        
        if (!$userRole) {
            return back()->with('error', 'Role User tidak ditemukan. Silakan hubungi administrator.');
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['role_id'] = $userRole->id;
        $validated['status_users'] = 'inactive'; // Pending approval by admin

        $user = User::create($validated);

        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login setelah akun Anda disetujui oleh admin.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Anda berhasil logout.');
    }
}
