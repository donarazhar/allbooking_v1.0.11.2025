<?php

namespace App\Http\Controllers;

use App\Models\BukaJadwal;
use App\Models\Catering;
use App\Models\TransaksiBooking;
use App\Models\User;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings
     * 
     * Super Admin: Lihat SEMUA booking dari semua cabang (readonly)
     * Admin Cabang: Lihat booking cabangnya saja (CRUD)
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->role->kode === 'SUPERADMIN';

        // Base query
        $query = TransaksiBooking::with([
            'bukaJadwal.sesi',
            'bukaJadwal.jenisAcara',
            'bukaJadwal.cabang',
            'user',
            'catering',
            'cabang'
        ])->withCount('transaksiPembayaran');

        // =====================
        // LOGIC: Filter berdasarkan role
        // =====================
        if ($isSuperAdmin) {
            // Super Admin: Lihat SEMUA booking dari semua cabang

            // Filter by cabang jika ada request
            if ($request->filled('cabang_id')) {
                $query->where('transaksi_booking.cabang_id', $request->cabang_id);
            }
        } else {
            // Admin Cabang: Hanya booking cabangnya
            $cabangId = $currentUser->cabang_id;
            $query->where('transaksi_booking.cabang_id', $cabangId);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('users.nama', 'like', "%{$search}%");
                });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('transaksi_booking.status_booking', $request->status);
        }

        // Get bookings
        $bookings = $query->orderBy('transaksi_booking.tgl_booking', 'desc')->get();

        // Get cabang list untuk filter (Super Admin)
        $cabangList = Cabang::orderBy('nama')->get();

        // Get current cabang info (Admin Cabang)
        $cabangInfo = !$isSuperAdmin ? Cabang::find($currentUser->cabang_id) : null;

        // Get data untuk modal (Admin Cabang only)
        if (!$isSuperAdmin) {
            $bukaJadwalList = BukaJadwal::with(['sesi', 'jenisAcara'])
                ->where('cabang_id', $currentUser->cabang_id)
                ->where('status_jadwal', 'available')
                ->orderBy('tanggal')
                ->get();

            // Get users yang sudah pernah booking di cabang ini
            $userIds = TransaksiBooking::where('cabang_id', $currentUser->cabang_id)
                ->distinct()
                ->pluck('user_id')
                ->toArray();
            $userList = User::whereIn('id', $userIds)
                ->where('status_users', 'active')
                ->orderBy('nama')
                ->get();

            // Get catering yang attached ke cabang ini
            $cateringList = Catering::whereHas('cabang', function ($q) use ($currentUser) {
                $q->where('cabang.id', $currentUser->cabang_id);
            })->orderBy('nama')->get();
        } else {
            $bukaJadwalList = collect();
            $userList = collect();
            $cateringList = collect();
        }

        return view('admin.transaksi.booking.index', compact(
            'bookings',
            'cabangList',
            'cabangInfo',
            'isSuperAdmin',
            'bukaJadwalList',
            'userList',
            'cateringList'
        ));
    }

    /**
     * Store a newly created booking
     * Only Admin Cabang can create
     */
    public function store(Request $request)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can create
        if ($currentUser->role->kode === 'SUPERADMIN') {
            abort(403, 'Super Admin tidak dapat menambah booking. Fitur ini khusus untuk Admin Cabang.');
        }

        $cabangId = $currentUser->cabang_id;

        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
                // User harus pernah booking di cabang ini
                function ($attribute, $value, $fail) use ($cabangId) {
                    $hasBooking = TransaksiBooking::where('user_id', $value)
                        ->where('cabang_id', $cabangId)
                        ->exists();
                    if (!$hasBooking) {
                        $fail('User belum pernah booking di cabang Anda.');
                    }
                },
            ],
            'bukajadwal_id' => [
                'required',
                'exists:buka_jadwal,id',
                // Jadwal harus dari cabang yang sama dan available
                function ($attribute, $value, $fail) use ($cabangId) {
                    $jadwal = BukaJadwal::find($value);
                    if ($jadwal && $jadwal->cabang_id !== $cabangId) {
                        $fail('Jadwal tidak tersedia di cabang Anda.');
                    }
                    if ($jadwal && $jadwal->status_jadwal !== 'available') {
                        $fail('Jadwal sudah dibooking.');
                    }
                },
            ],
            'tgl_booking' => 'required|date',
            'catering_id' => [
                'nullable',
                'exists:catering,id',
                // Catering harus attached ke cabang ini
                function ($attribute, $value, $fail) use ($cabangId) {
                    if ($value) {
                        $catering = Catering::find($value);
                        if ($catering && !$catering->cabang()->where('cabang.id', $cabangId)->exists()) {
                            $fail('Catering tidak tersedia di cabang Anda.');
                        }
                    }
                },
            ],
            'keterangan' => 'nullable|string|max:500',
            'status_booking' => 'required|in:active,inactive'
        ], [
            'user_id.required' => 'User harus dipilih',
            'bukajadwal_id.required' => 'Jadwal harus dipilih',
            'tgl_booking.required' => 'Tanggal booking harus diisi',
            'status_booking.required' => 'Status booking harus dipilih'
        ]);

        DB::beginTransaction();
        try {
            // Check: Apakah jadwal ini sudah ada booking aktif?
            $existingBooking = TransaksiBooking::where('bukajadwal_id', $validated['bukajadwal_id'])
                ->where('status_booking', 'active')
                ->exists();

            if ($existingBooking) {
                return back()
                    ->with('error', 'Jadwal ini sudah dibooking oleh user lain!')
                    ->withInput();
            }

            // Auto-set expired date (2 minggu dari tanggal booking)
            $validated['tgl_expired_booking'] = Carbon::parse($validated['tgl_booking'])->addWeeks(2);
            $validated['cabang_id'] = $cabangId;

            // Create booking
            $booking = TransaksiBooking::create($validated);

            // AUTO UPDATE: Status jadwal jadi "booked" jika booking active
            if ($validated['status_booking'] === 'active') {
                $bukaJadwal = BukaJadwal::find($validated['bukajadwal_id']);
                $bukaJadwal->update(['status_jadwal' => 'booked']);
            }

            DB::commit();

            return redirect()->route('admin.transaksi.booking.index')
                ->with('success', 'Booking berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating booking: ' . $e->getMessage());

            return back()
                ->with('error', 'Terjadi kesalahan saat membuat booking.')
                ->withInput();
        }
    }

    /**
     * Update the specified booking
     * Only Admin Cabang can update their own booking
     */
    public function update(Request $request, TransaksiBooking $booking)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can update
        if ($currentUser->role->kode === 'SUPERADMIN') {
            abort(403, 'Super Admin tidak dapat mengedit booking.');
        }

        // Check ownership
        if ($booking->cabang_id !== $currentUser->cabang_id) {
            abort(403, 'Anda tidak memiliki akses ke booking ini.');
        }

        $cabangId = $currentUser->cabang_id;

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'bukajadwal_id' => [
                'required',
                'exists:buka_jadwal,id',
                function ($attribute, $value, $fail) use ($cabangId) {
                    $jadwal = BukaJadwal::find($value);
                    if ($jadwal && $jadwal->cabang_id !== $cabangId) {
                        $fail('Jadwal tidak tersedia di cabang Anda.');
                    }
                },
            ],
            'tgl_booking' => 'required|date',
            'catering_id' => [
                'nullable',
                'exists:catering,id',
                function ($attribute, $value, $fail) use ($cabangId) {
                    if ($value) {
                        $catering = Catering::find($value);
                        if ($catering && !$catering->cabang()->where('cabang.id', $cabangId)->exists()) {
                            $fail('Catering tidak tersedia di cabang Anda.');
                        }
                    }
                },
            ],
            'keterangan' => 'nullable|string|max:500',
            'status_booking' => 'required|in:active,inactive'
        ]);

        DB::beginTransaction();
        try {
            // Update expired date jika tanggal booking berubah
            if ($booking->tgl_booking != $validated['tgl_booking']) {
                $validated['tgl_expired_booking'] = Carbon::parse($validated['tgl_booking'])->addWeeks(2);
            }

            // Check jika jadwal berubah
            if ($booking->bukajadwal_id != $validated['bukajadwal_id']) {
                // Check: Jadwal baru sudah ada booking aktif?
                $existingBooking = TransaksiBooking::where('bukajadwal_id', $validated['bukajadwal_id'])
                    ->where('status_booking', 'active')
                    ->where('id', '!=', $booking->id)
                    ->exists();

                if ($existingBooking) {
                    DB::rollBack();
                    return back()
                        ->with('error', 'Jadwal baru sudah dibooking!')
                        ->withInput();
                }

                // LOGIC: Jadwal lama
                $oldJadwal = BukaJadwal::find($booking->bukajadwal_id);
                $hasOtherBooking = TransaksiBooking::where('bukajadwal_id', $booking->bukajadwal_id)
                    ->where('status_booking', 'active')
                    ->where('id', '!=', $booking->id)
                    ->exists();

                if (!$hasOtherBooking) {
                    $oldJadwal->update(['status_jadwal' => 'available']);
                }

                // LOGIC: Jadwal baru
                if ($validated['status_booking'] === 'active') {
                    $newJadwal = BukaJadwal::find($validated['bukajadwal_id']);
                    $newJadwal->update(['status_jadwal' => 'booked']);
                }
            }

            $booking->update($validated);

            DB::commit();

            return redirect()->route('admin.transaksi.booking.index')
                ->with('success', 'Booking berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating booking: ' . $e->getMessage());

            return back()
                ->with('error', 'Terjadi kesalahan saat mengupdate booking.')
                ->withInput();
        }
    }

    /**
     * Remove the specified booking
     * Only Admin Cabang can delete their own booking
     */
    public function destroy(TransaksiBooking $booking)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can delete
        if ($currentUser->role->kode === 'SUPERADMIN') {
            abort(403, 'Super Admin tidak dapat menghapus booking.');
        }

        // Check ownership
        if ($booking->cabang_id !== $currentUser->cabang_id) {
            abort(403, 'Anda tidak memiliki akses ke booking ini.');
        }

        DB::beginTransaction();
        try {
            // Check if has payment
            $paymentCount = $booking->transaksiPembayaran()->count();

            if ($paymentCount > 0) {
                return redirect()->route('admin.transaksi.booking.index')
                    ->with('error', "Booking tidak dapat dihapus karena sudah memiliki {$paymentCount} pembayaran!");
            }

            $jadwalId = $booking->bukajadwal_id;
            $userName = $booking->user->nama ?? 'User';

            $booking->delete();

            // LOGIC: Cek apakah masih ada booking aktif lain di jadwal ini
            $hasOtherBooking = TransaksiBooking::where('bukajadwal_id', $jadwalId)
                ->where('status_booking', 'active')
                ->exists();

            if (!$hasOtherBooking) {
                $bukaJadwal = BukaJadwal::find($jadwalId);
                if ($bukaJadwal) {
                    $bukaJadwal->update(['status_jadwal' => 'available']);
                }
            }

            DB::commit();

            return redirect()->route('admin.transaksi.booking.index')
                ->with('success', "Booking '{$userName}' berhasil dihapus!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting booking: ' . $e->getMessage());

            return redirect()->route('admin.transaksi.booking.index')
                ->with('error', 'Terjadi kesalahan saat menghapus booking.');
        }
    }

    /**
     * Update status of booking
     * Only Admin Cabang can update status of their own booking
     */
    public function updateStatus(Request $request, TransaksiBooking $booking)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can update status
        if ($currentUser->role->kode === 'SUPERADMIN') {
            abort(403, 'Super Admin tidak dapat mengubah status booking.');
        }

        // Check ownership
        if ($booking->cabang_id !== $currentUser->cabang_id) {
            abort(403, 'Anda tidak memiliki akses ke booking ini.');
        }

        $validated = $request->validate([
            'status_booking' => 'required|in:active,inactive'
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $booking->status_booking;
            $newStatus = $validated['status_booking'];

            $booking->update(['status_booking' => $newStatus]);

            $bukaJadwal = $booking->bukaJadwal;

            // LOGIC: Inactive → Available (jika tidak ada booking aktif lain)
            if ($newStatus === 'inactive') {
                $hasOtherActiveBooking = TransaksiBooking::where('bukajadwal_id', $booking->bukajadwal_id)
                    ->where('status_booking', 'active')
                    ->where('id', '!=', $booking->id)
                    ->exists();

                if (!$hasOtherActiveBooking) {
                    $bukaJadwal->update(['status_jadwal' => 'available']);
                }
            }

            // LOGIC: Active → Booked
            if ($newStatus === 'active') {
                $hasOtherActiveBooking = TransaksiBooking::where('bukajadwal_id', $booking->bukajadwal_id)
                    ->where('status_booking', 'active')
                    ->where('id', '!=', $booking->id)
                    ->exists();

                if ($hasOtherActiveBooking) {
                    DB::rollBack();
                    return redirect()->route('admin.transaksi.booking.index')
                        ->with('error', 'Tidak dapat mengaktifkan booking karena jadwal sudah dibooking!');
                }

                $bukaJadwal->update(['status_jadwal' => 'booked']);
            }

            DB::commit();

            $statusText = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()->route('admin.transaksi.booking.index')
                ->with('success', "Booking berhasil {$statusText}!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating booking status: ' . $e->getMessage());

            return redirect()->route('admin.transaksi.booking.index')
                ->with('error', 'Terjadi kesalahan saat mengupdate status booking.');
        }
    }
}
