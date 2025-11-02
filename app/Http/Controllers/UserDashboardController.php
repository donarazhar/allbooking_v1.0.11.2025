<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BukaJadwal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserDashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $totalBooking = Booking::where('user_id', $user->id)->count();
        $pendingBooking = Booking::where('user_id', $user->id)->where('status_bookings', 'inctive')->count();
        $approvedBooking = Booking::where('user_id', $user->id)->where('status_bookings', 'active')->count();
        
        $recentBookings = Booking::with(['bukaJadwal.sesi', 'bukaJadwal.jenisAcara', 'catering'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('user.dashboard', compact('user', 'totalBooking', 'pendingBooking', 'approvedBooking', 'recentBookings'));
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
            'alamat' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'nama.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'foto.image' => 'File harus berupa gambar',
            'foto.max' => 'Ukuran foto maksimal 2MB'
        ]);

        if ($request->hasFile('foto')) {
            // Delete old photo
            if ($user->foto && file_exists(public_path('uploads/profile/' . $user->foto))) {
                unlink(public_path('uploads/profile/' . $user->foto));
            }
            
            $file = $request->file('foto');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/profile'), $filename);
            $validated['foto'] = $filename;
        }

        $user->update($validated);

        return redirect()->route('user.profile')
            ->with('success', 'Profile berhasil diupdate!');
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

        if (!Hash::check($validated['password_lama'], $user->password)) {
            return back()->with('error', 'Password lama tidak sesuai!');
        }

        $user->update([
            'password' => Hash::make($validated['password_baru'])
        ]);

        return redirect()->route('user.profile')
            ->with('success', 'Password berhasil diubah!');
    }

    public function booking()
    {
        $jadwalTersedia = BukaJadwal::with(['sesi', 'jenisAcara'])
            ->whereDate('tanggal', '>=', now())
            ->whereDoesntHave('bookings', function($query) {
                $query->where('status_bookings', 'active');
            })
            ->orderBy('tanggal', 'asc')
            ->get();

        return view('user.booking', compact('jadwalTersedia'));
    }

    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'buka_jadwal_id' => 'required|exists:buka_jadwal,id',
            'tanggal_booking' => 'required|date',
            'catering_id' => 'nullable|exists:catering,id',
            'keterangan' => 'nullable|string'
        ], [
            'buka_jadwal_id.required' => 'Jadwal harus dipilih',
            'tanggal_booking.required' => 'Tanggal booking harus diisi'
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status_bookings'] = 'inactive';
        $validated['tgl_expired_booking'] = \Carbon\Carbon::parse($validated['tanggal_booking'])->addWeeks(2);

        Booking::create($validated);

        return redirect()->route('user.booking')
            ->with('success', 'Booking berhasil diajukan! Silakan tunggu konfirmasi admin.');
    }

    public function myBookings()
    {
        $bookings = Booking::with(['bukaJadwal.sesi', 'bukaJadwal.jenisAcara', 'catering'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.my-bookings', compact('bookings'));
    }
}
