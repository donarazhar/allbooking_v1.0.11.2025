<?php

namespace App\Http\Controllers;

use App\Models\Catering;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CateringController extends Controller
{
    /**
     * Display a listing of catering
     * 
     * Super Admin: Lihat SEMUA catering
     * Admin Cabang: Lihat catering yang attached ke cabangnya
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->role->kode === 'SUPERADMIN';

        // Base query
        $query = Catering::query()->withCount('transaksiBooking');

        // =====================
        // LOGIC: Filter berdasarkan role
        // =====================
        if ($isSuperAdmin) {
            // Super Admin: Lihat SEMUA catering

            // Filter: Catering yang melayani cabang tertentu
            if ($request->filled('cabang_id')) {
                $query->whereHas('cabang', function ($q) use ($request) {
                    $q->where('cabang.id', $request->cabang_id);
                });
            }
        } else {
            // Admin Cabang: Hanya catering yang attached ke cabangnya
            $cabangId = $currentUser->cabang_id;

            $query->whereHas('cabang', function ($q) use ($cabangId) {
                $q->where('cabang.id', $cabangId);
            });
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('catering.nama', 'like', "%{$search}%")
                    ->orWhere('catering.email', 'like', "%{$search}%")
                    ->orWhere('catering.no_hp', 'like', "%{$search}%");
            });
        }

        // Get catering data with cabang relationship
        $catering = $query->with('cabang')->orderBy('catering.nama', 'asc')->get();

        // Get cabang list untuk filter (Super Admin)
        $cabangList = Cabang::orderBy('nama')->get();

        // Get current cabang info (Admin Cabang)
        $cabangInfo = !$isSuperAdmin ? Cabang::find($currentUser->cabang_id) : null;

        return view('admin.master.catering.index', compact(
            'catering',
            'cabangList',
            'cabangInfo',
            'isSuperAdmin'
        ));
    }

    /**
     * Store a newly created catering
     * Only Admin Cabang can create
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can create
        if ($currentUser->role->kode === 'SUPERADMIN') {
            abort(403, 'Super Admin tidak dapat menambah catering. Fitur ini khusus untuk Admin Cabang.');
        }

        $cabangId = $currentUser->cabang_id;

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:catering,email|max:255',
            'no_hp' => 'required|string|min:10|max:15|regex:/^[0-9]+$/|unique:catering,no_hp',
            'alamat' => 'required|string|max:500',
            'password' => 'required|string|min:8',
            'keterangan' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'nama.required' => 'Nama catering harus diisi',
            'nama.max' => 'Nama maksimal 100 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'no_hp.required' => 'No HP harus diisi',
            'no_hp.unique' => 'No HP sudah digunakan',
            'no_hp.min' => 'No HP minimal 10 digit',
            'no_hp.max' => 'No HP maksimal 15 digit',
            'no_hp.regex' => 'No HP hanya boleh berisi angka',
            'alamat.required' => 'Alamat harus diisi',
            'alamat.max' => 'Alamat maksimal 500 karakter',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'keterangan.max' => 'Keterangan maksimal 500 karakter',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'foto.max' => 'Ukuran foto maksimal 2MB'
        ]);

        DB::beginTransaction();
        try {
            // Handle foto upload
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/catering'), $filename);
                $validated['foto'] = $filename;
            }

            $validated['password'] = Hash::make($validated['password']);
            $validated['nama'] = trim($validated['nama']);
            $validated['email'] = strtolower(trim($validated['email']));

            // Create catering
            $catering = Catering::create($validated);

            // Attach catering to current cabang (pivot table)
            $catering->cabang()->attach($cabangId);

            DB::commit();

            return redirect()->route('admin.master.catering.index')
                ->with('success', 'Catering berhasil ditambahkan dan di-assign ke cabang Anda!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating catering: ' . $e->getMessage());

            // Delete uploaded foto if exists
            if (isset($validated['foto']) && file_exists(public_path('uploads/catering/' . $validated['foto']))) {
                unlink(public_path('uploads/catering/' . $validated['foto']));
            }

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menambahkan catering.')
                ->withInput();
        }
    }

    /**
     * Update the specified catering
     * Admin Cabang can update catering that attached to their branch
     */
    public function update(Request $request, Catering $catering)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can update
        if ($currentUser->role->kode === 'SUPERADMIN') {
            abort(403, 'Super Admin tidak dapat mengedit catering.');
        }

        $cabangId = $currentUser->cabang_id;

        // Check if catering is attached to current cabang
        if (!$catering->cabang()->where('cabang.id', $cabangId)->exists()) {
            abort(403, 'Anda tidak memiliki akses ke catering ini.');
        }

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
                'max:15',
                'regex:/^[0-9]+$/',
                Rule::unique('catering')->ignore($catering->id),
            ],
            'alamat' => 'required|string|max:500',
            'password' => 'nullable|string|min:8',
            'keterangan' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'nama.required' => 'Nama catering harus diisi',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email sudah digunakan',
            'no_hp.required' => 'No HP harus diisi',
            'no_hp.unique' => 'No HP sudah digunakan',
            'no_hp.regex' => 'No HP hanya boleh berisi angka',
            'alamat.required' => 'Alamat harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'foto.image' => 'File harus berupa gambar',
            'foto.max' => 'Ukuran foto maksimal 2MB'
        ]);

        DB::beginTransaction();
        try {
            // Handle foto upload
            if ($request->hasFile('foto')) {
                // Delete old foto
                if ($catering->foto && file_exists(public_path('uploads/catering/' . $catering->foto))) {
                    unlink(public_path('uploads/catering/' . $catering->foto));
                }

                $file = $request->file('foto');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/catering'), $filename);
                $validated['foto'] = $filename;
            }

            // Handle password update
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $validated['nama'] = trim($validated['nama']);
            $validated['email'] = strtolower(trim($validated['email']));

            $catering->update($validated);

            DB::commit();

            return redirect()->route('admin.master.catering.index')
                ->with('success', 'Catering berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating catering: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat mengupdate catering.')
                ->withInput();
        }
    }

    /**
     * Remove the specified catering from current cabang
     * Admin Cabang: Detach catering from their branch (not permanent delete)
     */
    public function destroy(Catering $catering)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can detach
        if ($currentUser->role->kode === 'SUPERADMIN') {
            abort(403, 'Super Admin tidak dapat menghapus catering.');
        }

        $cabangId = $currentUser->cabang_id;

        // Check if catering is attached to current cabang
        if (!$catering->cabang()->where('cabang.id', $cabangId)->exists()) {
            abort(403, 'Anda tidak memiliki akses ke catering ini.');
        }

        // Check if catering is being used in bookings for this cabang
        $bookingCount = $catering->transaksiBooking()
            ->where('cabang_id', $cabangId)
            ->count();

        if ($bookingCount > 0) {
            return redirect()->route('admin.master.catering.index')
                ->with('error', "Catering tidak dapat dihapus karena masih digunakan oleh {$bookingCount} booking di cabang Anda!");
        }

        try {
            // Detach catering from current cabang (not permanent delete)
            $catering->cabang()->detach($cabangId);

            // Check if catering is still attached to other cabang
            $otherCabangCount = $catering->cabang()->count();

            if ($otherCabangCount === 0) {
                // If not attached to any cabang anymore, delete permanently
                if ($catering->foto && file_exists(public_path('uploads/catering/' . $catering->foto))) {
                    unlink(public_path('uploads/catering/' . $catering->foto));
                }
                $catering->delete();

                return redirect()->route('admin.master.catering.index')
                    ->with('success', "Catering '{$catering->nama}' berhasil dihapus permanent (tidak digunakan cabang lain)!");
            } else {
                return redirect()->route('admin.master.catering.index')
                    ->with('success', "Catering '{$catering->nama}' berhasil di-remove dari cabang Anda!");
            }
        } catch (\Exception $e) {
            Log::error('Error removing catering: ' . $e->getMessage());
            return redirect()->route('admin.master.catering.index')
                ->with('error', 'Terjadi kesalahan saat menghapus catering.');
        }
    }
}
