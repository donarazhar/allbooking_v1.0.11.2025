<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BukaJadwal;
use App\Models\User;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserDashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Count bookings
        $totalBooking = Booking::where('user_id', $user->id)->count();
        $pendingBooking = Booking::where('user_id', $user->id)
            ->where('status_booking', 'inactive')
            ->count();
        $approvedBooking = Booking::where('user_id', $user->id)
            ->where('status_booking', 'active')
            ->count();

        // Get recent bookings with all relations
        $recentBookings = Booking::with([
            'bukaJadwal.sesi',
            'bukaJadwal.jenisAcara',
            'catering'
        ])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('user.dashboard', compact(
            'user',
            'totalBooking',
            'pendingBooking',
            'approvedBooking',
            'recentBookings'
        ));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'nama.required' => 'Nama harus diisi',
            'nama.max' => 'Nama maksimal 255 karakter',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'no_hp.max' => 'No HP maksimal 20 karakter',
            'alamat.max' => 'Alamat maksimal 500 karakter',
            'foto.image' => 'File harus berupa gambar',
            'foto.mimes' => 'Format foto harus jpeg, png, atau jpg',
            'foto.max' => 'Ukuran foto maksimal 2MB'
        ]);

        DB::beginTransaction();
        try {
            if ($request->hasFile('foto')) {
                // Delete old photo if exists
                if ($user->foto && file_exists(public_path('uploads/profile/' . $user->foto))) {
                    unlink(public_path('uploads/profile/' . $user->foto));
                }

                // Upload new photo
                $file = $request->file('foto');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/profile'), $filename);
                $validated['foto'] = $filename;
            }

            $user->update($validated);

            DB::commit();

            return redirect()->route('user.profile')
                ->with('success', 'Profile berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Cleanup uploaded file if error
            if (isset($validated['foto']) && file_exists(public_path('uploads/profile/' . $validated['foto']))) {
                unlink(public_path('uploads/profile/' . $validated['foto']));
            }

            Log::error('Error updating profile: ' . $e->getMessage());

            return back()
                ->with('error', 'Terjadi kesalahan saat mengupdate profile.')
                ->withInput();
        }
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6|confirmed',
        ], [
            'password_lama.required' => 'Password lama harus diisi',
            'password_baru.required' => 'Password baru harus diisi',
            'password_baru.min' => 'Password baru minimal 6 karakter',
            'password_baru.confirmed' => 'Konfirmasi password tidak cocok'
        ]);

        $user = Auth::user();

        // Verify old password
        if (!Hash::check($validated['password_lama'], $user->password)) {
            return back()->with('error', 'Password lama tidak sesuai!');
        }

        DB::beginTransaction();
        try {
            $user->update([
                'password' => Hash::make($validated['password_baru'])
            ]);

            DB::commit();

            return redirect()->route('user.profile')
                ->with('success', 'Password berhasil diubah!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating password: ' . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan saat mengubah password.');
        }
    }

    public function booking()
    {
        // Get available jadwal (future dates, not booked)
        $jadwalTersedia = BukaJadwal::with(['sesi', 'jenisAcara'])
            ->whereDate('tanggal', '>=', now())
            ->where('status_jadwal', 'available')
            ->whereDoesntHave('bookings', function ($query) {
                $query->where('status_booking', 'active');
            })
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('user.jadwal', compact('jadwalTersedia'));
    }

    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'bukajadwal_id' => 'required|exists:buka_jadwal,id',
            'tgl_booking' => 'required|date',
            'catering_id' => 'nullable|exists:catering,id',
            'keterangan' => 'nullable|string|max:1000'
        ], [
            'bukajadwal_id.required' => 'Jadwal harus dipilih',
            'bukajadwal_id.exists' => 'Jadwal tidak valid',
            'tgl_booking.required' => 'Tanggal booking harus diisi',
            'tgl_booking.date' => 'Format tanggal tidak valid',
            'catering_id.exists' => 'Catering tidak valid',
            'keterangan.max' => 'Keterangan maksimal 1000 karakter'
        ]);

        DB::beginTransaction();
        try {
            // Check if jadwal still available
            $jadwal = BukaJadwal::find($validated['bukajadwal_id']);

            if (!$jadwal || $jadwal->status_jadwal !== 'available') {
                return back()->with('error', 'Jadwal sudah tidak tersedia!');
            }

            // Create booking
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'bukajadwal_id' => $validated['bukajadwal_id'],
                'tgl_booking' => $validated['tgl_booking'],
                'tgl_expired_booking' => \Carbon\Carbon::parse($validated['tgl_booking'])->addWeeks(2),
                'status_booking' => 'inactive', // Pending approval
                'catering_id' => $validated['catering_id'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('user.my-bookings')
                ->with('success', 'Booking berhasil diajukan! Silakan tunggu konfirmasi admin.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating booking: ' . $e->getMessage());

            return back()
                ->with('error', 'Terjadi kesalahan saat membuat booking.')
                ->withInput();
        }
    }

    public function myBookings()
    {
        $bookings = Booking::with([
            'bukaJadwal.sesi',
            'bukaJadwal.jenisAcara',
            'catering',
            'pembayaran'
        ])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.my-bookings', compact('bookings'));
    }

    public function bayar()
    {
        // Get bookings that can be paid
        $bookings = Booking::with([
            'bukaJadwal.sesi',
            'bukaJadwal.jenisAcara',
            'catering',
            'pembayaran'
        ])
            ->where('user_id', Auth::id())
            ->whereIn('status_booking', ['active', 'inactive'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.bayar', compact('bookings'));
    }

    public function storeBayar(Request $request)
    {
        // ✅ FIX 1: Add detailed logging
        Log::info('Payment attempt started', [
            'user_id' => Auth::id(),
            'request_data' => $request->except(['bukti_bayar'])
        ]);

        $validated = $request->validate([
            'booking_id' => 'required|exists:transaksi_booking,id',
            'tgl_pembayaran' => 'required|date',
            'jenis_bayar' => 'required|in:DP,Termin 1,Termin 2,Termin 3,Termin 4,Pelunasan',
            'nominal' => 'required|numeric|min:1000|max:999999999',
            'bukti_bayar' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'booking_id.required' => 'Booking harus dipilih',
            'booking_id.exists' => 'Booking tidak valid',
            'tgl_pembayaran.required' => 'Tanggal pembayaran harus diisi',
            'tgl_pembayaran.date' => 'Format tanggal tidak valid',
            'jenis_bayar.required' => 'Jenis bayar harus dipilih',
            'jenis_bayar.in' => 'Jenis bayar tidak valid',
            'nominal.required' => 'Nominal harus diisi',
            'nominal.numeric' => 'Nominal harus berupa angka',
            'nominal.min' => 'Nominal minimal Rp 1.000',
            'nominal.max' => 'Nominal maksimal Rp 999.999.999',
            'bukti_bayar.required' => 'Bukti bayar harus diupload',
            'bukti_bayar.image' => 'Bukti bayar harus berupa gambar',
            'bukti_bayar.mimes' => 'Format bukti bayar harus jpeg, png, atau jpg',
            'bukti_bayar.max' => 'Ukuran bukti bayar maksimal 2MB'
        ]);

        Log::info('Validation passed', ['validated_data' => $validated]);

        // Verify booking belongs to current user
        $booking = Booking::where('id', $validated['booking_id'])
            ->where('user_id', Auth::id())
            ->first();

        if (!$booking) {
            Log::warning('Booking not found or unauthorized', [
                'booking_id' => $validated['booking_id'],
                'user_id' => Auth::id()
            ]);
            return back()->with('error', 'Booking tidak ditemukan!');
        }

        Log::info('Booking verified', ['booking_id' => $booking->id]);

        DB::beginTransaction();
        try {
            // ✅ FIX 2: Create uploads directory if not exists
            $uploadPath = public_path('uploads/bukti_bayar');
            if (!file_exists($uploadPath)) {
                Log::info('Creating upload directory', ['path' => $uploadPath]);
                mkdir($uploadPath, 0755, true);
            }

            // Upload bukti bayar
            $filename = null;
            if ($request->hasFile('bukti_bayar')) {
                Log::info('Processing file upload');

                $file = $request->file('bukti_bayar');

                // ✅ FIX 3: Better filename generation
                $filename = 'bayar_' . $booking->id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                Log::info('Uploading file', [
                    'filename' => $filename,
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType()
                ]);

                // ✅ FIX 4: Use try-catch for file upload
                try {
                    $file->move($uploadPath, $filename);
                    Log::info('File uploaded successfully', ['path' => $uploadPath . '/' . $filename]);
                } catch (\Exception $e) {
                    Log::error('File upload failed', [
                        'error' => $e->getMessage(),
                        'path' => $uploadPath,
                        'filename' => $filename
                    ]);
                    throw $e;
                }
            } else {
                Log::error('No file uploaded');
                throw new \Exception('Bukti bayar tidak ditemukan');
            }

            // ✅ FIX 5: Prepare data for insertion
            $pembayaranData = [
                'booking_id' => $validated['booking_id'],
                'tgl_pembayaran' => $validated['tgl_pembayaran'],
                'jenis_bayar' => $validated['jenis_bayar'],
                'nominal' => $validated['nominal'],
                'bukti_bayar' => $filename,
            ];

            Log::info('Creating pembayaran record', ['data' => $pembayaranData]);

            // Create pembayaran
            $pembayaran = Pembayaran::create($pembayaranData);

            Log::info('Pembayaran created successfully', ['id' => $pembayaran->id]);

            // If this is DP payment, activate booking and remove expired date
            if ($validated['jenis_bayar'] === 'DP') {
                Log::info('DP payment detected, activating booking');

                $booking->update([
                    'status_booking' => 'active',
                    'tgl_expired_booking' => null
                ]);

                Log::info('Booking activated', ['booking_id' => $booking->id]);
            }

            DB::commit();

            Log::info('Payment transaction completed successfully', [
                'pembayaran_id' => $pembayaran->id,
                'booking_id' => $booking->id
            ]);

            return redirect()->route('user.bayar')
                ->with('success', 'Pembayaran berhasil diajukan! Admin akan memverifikasi pembayaran Anda.');
        } catch (\Exception $e) {
            DB::rollBack();

            // ✅ FIX 6: Enhanced error logging
            Log::error('Payment submission failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'booking_id' => $validated['booking_id'] ?? null,
                'user_id' => Auth::id(),
                'file_uploaded' => isset($filename) ? $filename : 'no'
            ]);

            // Cleanup uploaded file if error
            if (isset($filename) && file_exists(public_path('uploads/bukti_bayar/' . $filename))) {
                Log::info('Cleaning up uploaded file', ['filename' => $filename]);
                unlink(public_path('uploads/bukti_bayar/' . $filename));
            }

            // ✅ FIX 7: Return detailed error in development
            if (config('app.debug')) {
                return back()
                    ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                    ->withInput();
            }

            return back()
                ->with('error', 'Terjadi kesalahan saat mengajukan pembayaran. Silakan coba lagi.')
                ->withInput();
        }
    }
}
