<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BukaJadwal;
use App\Models\Catering;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

// Controller untuk mengelola data booking dari sisi Admin.
class BookingController extends Controller
{
    /**
     * Menampilkan daftar semua booking.
     */
    public function index()
    {
        // Mengambil data booking beserta relasi-relasinya.
        $bookings = Booking::with(['bukaJadwal.sesi', 'bukaJadwal.jenisAcara', 'user', 'catering'])
            ->orderBy('tanggal_booking', 'desc')
            ->get();
        return view('transaksi.booking.index', compact('bookings'));
    }

    /**
     * Menyimpan data booking baru.
     */
    public function store(Request $request)
    {
        // Validasi input.
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_booking' => 'required|date',
            'buka_jadwal_id' => 'required|exists:buka_jadwal,id',
            'catering_id' => 'nullable|exists:catering,id',
            'status_bookings' => 'required|in:active,inactive',
            'keterangan' => 'nullable|string'
        ], [
            // Pesan error kustom.
            'user_id.required' => 'User harus dipilih',
            'tanggal_booking.required' => 'Tanggal booking harus diisi',
            'buka_jadwal_id.required' => 'Jadwal harus dipilih',
            'status_bookings.required' => 'Status harus dipilih'
        ]);

        // Menghitung otomatis tanggal expired, yaitu 2 minggu dari tanggal booking.
        $validated['tgl_expired_booking'] = Carbon::parse($validated['tanggal_booking'])->addWeeks(2);

        // Membuat data booking.
        Booking::create($validated);

        // Redirect dengan pesan sukses.
        return redirect()->route('booking.index')
            ->with('success', 'Booking berhasil ditambahkan! Batas pembayaran: ' . Carbon::parse($validated['tgl_expired_booking'])->format('d M Y'));
    }

    /**
     * Mengupdate data booking yang ada.
     */
    public function update(Request $request, Booking $booking)
    {
        // Validasi input.
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_booking' => 'required|date',
            'buka_jadwal_id' => 'required|exists:buka_jadwal,id',
            'catering_id' => 'nullable|exists:catering,id',
            'status_bookings' => 'required|in:active,inactive',
            'keterangan' => 'nullable|string'
        ], [
            'user_id.required' => 'User harus dipilih',
            'tanggal_booking.required' => 'Tanggal booking harus diisi',
            'buka_jadwal_id.required' => 'Jadwal harus dipilih',
            'status_bookings.required' => 'Status harus dipilih'
        ]);

        // Hitung ulang tanggal expired jika tanggal booking diubah.
        if ($booking->tanggal_booking != $validated['tanggal_booking']) {
            $validated['tgl_expired_booking'] = Carbon::parse($validated['tanggal_booking'])->addWeeks(2);
        }

        // Update data.
        $booking->update($validated);

        return redirect()->route('booking.index')
            ->with('success', 'Booking berhasil diupdate!');
    }

    /**
     * Menghapus data booking.
     */
    public function destroy(Booking $booking)
    {
        try {
            $booking->delete();
            return redirect()->route('booking.index')
                ->with('success', 'Booking berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('booking.index')
                ->with('error', 'Booking tidak dapat dihapus!');
        }
    }

    /**
     * Mengubah status booking secara spesifik (misal: dari modal).
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status_bookings' => 'required|in:active,inactive'
        ]);

        $booking->update($validated);

        return redirect()->route('booking.index')
            ->with('success', 'Status booking berhasil diubah menjadi ' . $validated['status_bookings'] . '!');
    }
}
