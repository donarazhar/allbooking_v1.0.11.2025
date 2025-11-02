<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BukaJadwal;
use App\Models\Catering;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['bukaJadwal.sesi', 'bukaJadwal.jenisAcara', 'user', 'catering'])
            ->orderBy('tanggal_booking', 'desc')
            ->get();
        return view('transaksi.booking.index', compact('bookings'));
    }

    public function store(Request $request)
    {
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

        // Auto-calculate tgl_expired_booking (2 minggu dari tanggal booking)
        $validated['tgl_expired_booking'] = Carbon::parse($validated['tanggal_booking'])->addWeeks(2);

        Booking::create($validated);

        return redirect()->route('booking.index')
            ->with('success', 'Booking berhasil ditambahkan! Batas pembayaran: ' . Carbon::parse($validated['tgl_expired_booking'])->format('d M Y'));
    }

    public function update(Request $request, Booking $booking)
    {
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

        // Recalculate tgl_expired_booking jika tanggal_booking berubah
        if ($booking->tanggal_booking != $validated['tanggal_booking']) {
            $validated['tgl_expired_booking'] = Carbon::parse($validated['tanggal_booking'])->addWeeks(2);
        }

        $booking->update($validated);

        return redirect()->route('booking.index')
            ->with('success', 'Booking berhasil diupdate!');
    }

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
