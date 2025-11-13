<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Cabang;
use App\Models\TransaksiBooking;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    const PROTECTED_USERS = ['superadmin@alazhar.or.id']; // Protected super admin email

    /**
     * Display a listing of users
     * 
     * Super Admin: Lihat SEMUA user dari semua cabang
     * Admin Cabang: Lihat user yang booking di cabangnya + user yang dia tambahkan
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->role->kode === 'SUPERADMIN';

        // Base query with relationships
        $query = User::with(['role', 'cabang'])
            ->withCount('transaksiBooking')
            ->where('users.id', '!=', $currentUser->id); // Exclude current user

        // =====================
        // LOGIC: Filter berdasarkan role
        // =====================
        if ($isSuperAdmin) {
            // Super Admin: Lihat SEMUA user
            // No additional filter

        } else {
            // Admin Cabang: User yang booking di cabangnya + user yang dia tambahkan (created_by)
            $cabangId = $currentUser->cabang_id;

            // Get user IDs yang pernah booking di cabang ini
            $userIdsWhoBooked = TransaksiBooking::where('cabang_id', $cabangId)
                ->distinct()
                ->pluck('user_id')
                ->toArray();

            // Get user IDs yang ditambahkan oleh admin cabang ini
            $userIdsCreatedByAdmin = User::where('created_by', $currentUser->id)
                ->pluck('id')
                ->toArray();

            // Merge both arrays
            $allowedUserIds = array_unique(array_merge($userIdsWhoBooked, $userIdsCreatedByAdmin));

            if (empty($allowedUserIds)) {
                // Jika belum ada, return empty
                $query->whereRaw('1 = 0'); // Force empty result
            } else {
                $query->whereIn('users.id', $allowedUserIds);
            }
        }

        // =====================
        // Search filter
        // =====================
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('users.nama', 'like', "%{$search}%")
                    ->orWhere('users.email', 'like', "%{$search}%")
                    ->orWhere('users.no_hp', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role_id')) {
            $query->where('users.role_id', $request->role_id);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('users.status_users', $request->status);
        }

        // Cabang filter (Super Admin only)
        if ($isSuperAdmin && $request->filled('cabang_id')) {
            $query->where('users.cabang_id', $request->cabang_id);
        }

        // Get users with pagination
        $users = $query->orderBy('users.nama', 'asc')->paginate(20);

        // Get filter options
        $roles = Role::all();
        $cabangList = Cabang::orderBy('nama')->get();

        // Get current cabang info (Admin Cabang)
        $cabangInfo = !$isSuperAdmin ? Cabang::find($currentUser->cabang_id) : null;

        // Statistics
        if ($isSuperAdmin) {
            $stats = [
                'total' => User::where('id', '!=', $currentUser->id)->count(),
                'active' => User::where('id', '!=', $currentUser->id)->where('status_users', 'active')->count(),
                'inactive' => User::where('id', '!=', $currentUser->id)->where('status_users', 'inactive')->count(),
            ];
        } else {
            $cabangId = $currentUser->cabang_id;
            $userIdsWhoBooked = TransaksiBooking::where('cabang_id', $cabangId)
                ->distinct()
                ->pluck('user_id')
                ->toArray();
            $userIdsCreatedByAdmin = User::where('created_by', $currentUser->id)
                ->pluck('id')
                ->toArray();
            $allowedUserIds = array_unique(array_merge($userIdsWhoBooked, $userIdsCreatedByAdmin));

            $stats = [
                'total' => count($allowedUserIds),
                'active' => User::whereIn('id', $allowedUserIds)->where('status_users', 'active')->count(),
                'inactive' => User::whereIn('id', $allowedUserIds)->where('status_users', 'inactive')->count(),
            ];
        }

        return view('admin.manajemen.users.index', compact(
            'users',
            'roles',
            'cabangList',
            'cabangInfo',
            'stats',
            'isSuperAdmin'
        ));
    }

    /**
     * Show the form for creating a new user
     * Super Admin: Bisa tambah Admin Cabang & Pimpinan Cabang
     * Admin Cabang: Bisa tambah User saja
     */
    public function create()
    {
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->role->kode === 'SUPERADMIN';

        if ($isSuperAdmin) {
            // Super Admin: Hanya role Admin Cabang dan Pimpinan Cabang
            $roles = Role::whereIn('kode', ['ADMIN', 'PIMPINAN'])->get();
            $cabangList = Cabang::orderBy('nama')->get();
        } else {
            // Admin Cabang: Hanya role User
            $roles = Role::where('kode', 'USER')->get();
            $cabangList = collect([Cabang::find($currentUser->cabang_id)]); // Hanya cabang admin
        }

        return view('admin.manajemen.users.create', compact('roles', 'cabangList', 'isSuperAdmin'));
    }

    /**
     * Store a newly created user
     * Super Admin: Bisa tambah Admin Cabang & Pimpinan Cabang
     * Admin Cabang: Bisa tambah User saja
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->role->kode === 'SUPERADMIN';

        // Validation rules
        $rules = [
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email|max:255',
            'no_hp' => 'required|string|min:10|max:15|regex:/^[0-9]+$/|unique:users,no_hp',
            'alamat' => 'nullable|string|max:500',
            'password' => 'required|string|min:8|confirmed',
            'status_users' => 'required|in:active,inactive',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ];

        if ($isSuperAdmin) {
            // Super Admin: Validasi role harus Admin Cabang atau Pimpinan Cabang
            $rules['role_id'] = [
                'required',
                'exists:roles,id',
                function ($attribute, $value, $fail) {
                    $role = Role::find($value);
                    if ($role && !in_array($role->kode, ['ADMIN', 'PIMPINAN'])) {
                        $fail('Hanya dapat menambah user dengan role Admin Cabang atau Pimpinan Cabang.');
                    }
                },
            ];
            $rules['cabang_id'] = 'required|exists:cabang,id';
        } else {
            // Admin Cabang: Validasi role harus User
            $rules['role_id'] = [
                'required',
                'exists:roles,id',
                function ($attribute, $value, $fail) {
                    $role = Role::find($value);
                    if ($role && $role->kode !== 'USER') {
                        $fail('Anda hanya dapat menambah user dengan role User.');
                    }
                },
            ];
            // Cabang otomatis dari admin yang login
        }

        $messages = [
            'nama.required' => 'Nama harus diisi',
            'nama.max' => 'Nama maksimal 100 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'no_hp.required' => 'Nomor HP harus diisi',
            'no_hp.unique' => 'Nomor HP sudah terdaftar',
            'no_hp.min' => 'Nomor HP minimal 10 digit',
            'no_hp.max' => 'Nomor HP maksimal 15 digit',
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'role_id.required' => 'Role harus dipilih',
            'cabang_id.required' => 'Cabang harus dipilih',
            'status_users.required' => 'Status harus dipilih',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'foto.max' => 'Ukuran gambar maksimal 2MB',
        ];

        $validated = $request->validate($rules, $messages);

        DB::beginTransaction();
        try {
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

            // Set cabang_id untuk Admin Cabang
            if (!$isSuperAdmin) {
                $validated['cabang_id'] = $currentUser->cabang_id;
            }

            // Track who created this user
            $validated['created_by'] = $currentUser->id;

            User::create($validated);

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
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

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->role->kode === 'SUPERADMIN';

        // Check access for Admin Cabang
        if (!$isSuperAdmin) {
            $hasBooking = TransaksiBooking::where('cabang_id', $currentUser->cabang_id)
                ->where('user_id', $user->id)
                ->exists();

            $isCreatedByAdmin = $user->created_by === $currentUser->id;

            if (!$hasBooking && !$isCreatedByAdmin) {
                abort(403, 'Anda tidak memiliki akses ke user ini.');
            }
        }

        $user->load(['role', 'cabang', 'transaksiBooking.bukaJadwal.jenisAcara']);

        // Get booking statistics
        $bookingStats = [
            'total' => $user->transaksiBooking()->count(),
            'active' => $user->transaksiBooking()->where('status_booking', 'active')->count(),
            'inactive' => $user->transaksiBooking()->where('status_booking', 'inactive')->count(),
            'completed' => $user->transaksiBooking()
                ->whereHas('transaksiPembayaran', function ($q) {
                    $q->where('jenis_bayar', 'Pelunasan');
                })
                ->count(),
        ];

        return view('admin.manajemen.users.show', compact('user', 'bookingStats', 'isSuperAdmin'));
    }

    /**
     * Show the form for editing the specified user
     * Super Admin: Bisa edit Admin Cabang & Pimpinan Cabang
     * Admin Cabang: Bisa edit User yang dia tambahkan saja
     */
    public function edit(User $user)
    {
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->role->kode === 'SUPERADMIN';

        // Check access
        if ($isSuperAdmin) {
            // Super Admin bisa edit semua (kecuali protected user)
            if (in_array($user->email, self::PROTECTED_USERS)) {
                // Protected user masih bisa diedit tapi dengan batasan
            }
        } else {
            // Admin Cabang hanya bisa edit user yang dia buat
            if ($user->created_by !== $currentUser->id) {
                abort(403, 'Anda hanya dapat mengedit user yang Anda tambahkan sendiri.');
            }
        }

        if ($isSuperAdmin) {
            $roles = Role::whereIn('kode', ['ADMIN', 'PIMPINAN'])->get();
            $cabangList = Cabang::orderBy('nama')->get();
        } else {
            $roles = Role::where('kode', 'USER')->get();
            $cabangList = collect([Cabang::find($currentUser->cabang_id)]);
        }

        return view('admin.manajemen.users.edit', compact('user', 'roles', 'cabangList', 'isSuperAdmin'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->role->kode === 'SUPERADMIN';

        // Check access
        if (!$isSuperAdmin) {
            if ($user->created_by !== $currentUser->id) {
                abort(403, 'Anda hanya dapat mengedit user yang Anda tambahkan sendiri.');
            }
        }

        // Check if protected user (Super Admin utama)
        if (in_array($user->email, self::PROTECTED_USERS)) {
            // Protected user: Cannot change email, role & status
            $validated = $request->validate([
                'nama' => 'required|string|max:100',
                'no_hp' => [
                    'required',
                    'string',
                    'min:10',
                    'max:15',
                    'regex:/^[0-9]+$/',
                    Rule::unique('users')->ignore($user->id),
                ],
                'alamat' => 'nullable|string|max:500',
                'cabang_id' => 'required|exists:cabang,id',
                'password' => 'nullable|string|min:8|confirmed',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);
        } else {
            // Regular user
            $rules = [
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
                    'max:15',
                    'regex:/^[0-9]+$/',
                    Rule::unique('users')->ignore($user->id),
                ],
                'alamat' => 'nullable|string|max:500',
                'password' => 'nullable|string|min:8|confirmed',
                'status_users' => 'required|in:active,inactive',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ];

            if ($isSuperAdmin) {
                $rules['role_id'] = [
                    'required',
                    'exists:roles,id',
                    function ($attribute, $value, $fail) {
                        $role = Role::find($value);
                        if ($role && !in_array($role->kode, ['ADMIN', 'PIMPINAN'])) {
                            $fail('Hanya dapat mengubah ke role Admin Cabang atau Pimpinan Cabang.');
                        }
                    },
                ];
                $rules['cabang_id'] = 'required|exists:cabang,id';
            } else {
                $rules['role_id'] = [
                    'required',
                    'exists:roles,id',
                    function ($attribute, $value, $fail) {
                        $role = Role::find($value);
                        if ($role && $role->kode !== 'USER') {
                            $fail('Anda hanya dapat menggunakan role User.');
                        }
                    },
                ];
            }

            $validated = $request->validate($rules);
            $validated['email'] = strtolower(trim($validated['email']));
        }

        DB::beginTransaction();
        try {
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

            // Set cabang_id untuk Admin Cabang jika tidak ada di request
            if (!$isSuperAdmin && !isset($validated['cabang_id'])) {
                $validated['cabang_id'] = $currentUser->cabang_id;
            }

            $user->update($validated);

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengupdate user.')
                ->withInput();
        }
    }

    /**
     * Remove the specified user
     * Super Admin: Bisa hapus Admin/Pimpinan yang tidak punya booking
     * Admin Cabang: Bisa hapus User yang dia tambahkan (tidak punya booking)
     */
    public function destroy(User $user)
    {
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->role->kode === 'SUPERADMIN';

        // Protect super admin
        if (in_array($user->email, self::PROTECTED_USERS)) {
            return redirect()->route('admin.users.index')
                ->with('error', 'User Super Admin tidak dapat dihapus!');
        }

        // Prevent deleting own account
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun sendiri!');
        }

        // Check access for Admin Cabang
        if (!$isSuperAdmin) {
            if ($user->created_by !== $currentUser->id) {
                abort(403, 'Anda hanya dapat menghapus user yang Anda tambahkan sendiri.');
            }
        }

        // Check if user has transaksiBooking
        $bookingCount = $user->transaksiBooking()->count();

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

    /**
     * Toggle user status (active/inactive)
     * Super Admin: Bisa toggle semua user (kecuali protected)
     * Admin Cabang: Bisa toggle user yang dia tambahkan saja
     */
    public function toggleStatus(User $user)
    {
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->role->kode === 'SUPERADMIN';

        // Protect super admin
        if (in_array($user->email, self::PROTECTED_USERS)) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Status Super Admin tidak dapat diubah!');
        }

        // Check access for Admin Cabang
        if (!$isSuperAdmin) {
            if ($user->created_by !== $currentUser->id) {
                abort(403, 'Anda hanya dapat mengubah status user yang Anda tambahkan sendiri.');
            }
        }

        $newStatus = $user->status_users === 'active' ? 'inactive' : 'active';
        $user->update(['status_users' => $newStatus]);

        $statusText = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->nama}' berhasil {$statusText}!");
    }
}
