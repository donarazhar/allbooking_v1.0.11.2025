<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        // Redirect if already logged in
        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Validate input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi'
        ]);

        // Attempt login
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Check user status
            if ($user->status_users !== 'active') {
                Auth::logout();
                return back()->with('error', 'Akun Anda belum diaktifkan. Silakan hubungi administrator.');
            }

            // Log login activity (using Laravel's default Log)
            Log::info('User logged in', [
                'user_id' => $user->id,
                'user_name' => $user->nama,
                'ip' => $request->ip(),
            ]);

            // Redirect based on role
            return $this->redirectToDashboard($user)
                ->with('success', 'Selamat datang, ' . $user->nama . '!');
        }

        // Login failed
        return back()
            ->with('error', 'Email atau password salah!')
            ->withInput($request->only('email'));
    }

    /**
     * Show register form
     */
    public function showRegister()
    {
        // Redirect if already logged in
        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user());
        }

        // Tidak perlu kirim cabangList karena user tidak pilih cabang saat register
        return view('auth.register');
    }

    /**
     * Handle register request
     */
    public function register(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email|max:255',
            'no_hp' => 'required|string|unique:users,no_hp|min:10|max:15|regex:/^[0-9]+$/',
            'alamat' => 'nullable|string|max:500',
            'password' => 'required|string|min:8|confirmed',
        ], [
            // Nama validation messages
            'nama.required' => 'Nama lengkap harus diisi',
            'nama.max' => 'Nama maksimal 100 karakter',

            // Email validation messages
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar, gunakan email lain',

            // No HP validation messages
            'no_hp.required' => 'Nomor HP harus diisi',
            'no_hp.unique' => 'Nomor HP sudah terdaftar',
            'no_hp.min' => 'Nomor HP minimal 10 digit',
            'no_hp.max' => 'Nomor HP maksimal 15 digit',
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka',

            // Password validation messages
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok'
        ]);

        // Get User role (kode = 'USER')
        $userRole = Role::where('kode', 'USER')->first();

        if (!$userRole) {
            return back()
                ->with('error', 'Role User tidak ditemukan. Silakan hubungi administrator.')
                ->withInput();
        }

        // Get default cabang (first cabang) untuk user umum
        $defaultCabang = Cabang::first();

        if (!$defaultCabang) {
            return back()
                ->with('error', 'Data cabang belum tersedia. Silakan hubungi administrator.')
                ->withInput();
        }

        // Create new user with transaction
        DB::beginTransaction();
        try {
            $user = User::create([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'],
                'alamat' => $validated['alamat'] ?? null,
                'password' => Hash::make($validated['password']),
                'role_id' => $userRole->id,
                'cabang_id' => $defaultCabang->id,
                'status_users' => 'inactive', // Pending approval
            ]);

            DB::commit();

            // Log registration
            Log::info('New user registered', [
                'user_id' => $user->id,
                'user_name' => $user->nama,
                'user_email' => $user->email,
            ]);

            return redirect()
                ->route('login')
                ->with('success', 'Registrasi berhasil! Akun Anda menunggu persetujuan Admin. Anda akan dapat login setelah akun diaktifkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'email' => $validated['email'],
            ]);

            return back()
                ->with('error', 'Terjadi kesalahan saat registrasi. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Log logout activity
        if ($user) {
            Log::info('User logged out', [
                'user_id' => $user->id,
                'user_name' => $user->nama,
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Anda berhasil logout.');
    }

    /**
     * Redirect user to appropriate dashboard based on role
     * 
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToDashboard($user)
    {
        // Check if user has role relationship
        if (!$user->role) {
            Auth::logout();
            return redirect()
                ->route('login')
                ->with('error', 'Role pengguna tidak ditemukan. Silakan hubungi administrator.');
        }

        // Redirect based on role KODE (more reliable than nama)
        $roleKode = $user->role->kode;

        return match ($roleKode) {
            'SUPERADMIN' => redirect()->route('admin.dashboard'),
            'ADMIN' => redirect()->route('admin.dashboard'),
            'PIMPINAN' => redirect()->route('pimpinan.dashboard'),
            'USER' => redirect()->route('user.dashboard'),
            default => redirect()->route('user.dashboard'),
        };
    }
}
