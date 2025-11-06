<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BukaJadwal;
use App\Models\Catering;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with([
                'bukaJadwal.sesi', 
                'bukaJadwal.jenisAcara', 
                'user', 
                'catering'
            ])
            ->orderBy('tgl_booking', 'desc')
            ->get();
            
        return view('transaksi.booking.index', compact('bookings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'bukajadwal_id' => 'required|exists:buka_jadwal,id',
            'tgl_booking' => 'required|date',
            'catering_id' => 'nullable|exists:catering,id',
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
            $existingBooking = Booking::where('bukajadwal_id', $validated['bukajadwal_id'])
                ->where('status_booking', 'active')
                ->exists();
            
            if ($existingBooking) {
                return back()
                    ->with('error', 'Jadwal ini sudah dibooking oleh user lain!')
                    ->withInput();
            }

            // Auto-set expired date (2 minggu dari tanggal booking)
            $validated['tgl_expired_booking'] = Carbon::parse($validated['tgl_booking'])->addWeeks(2);

            // Create booking
            $booking = Booking::create($validated);

            // AUTO UPDATE: Status jadwal jadi "booked" karena ada booking aktif
            $bukaJadwal = BukaJadwal::find($validated['bukajadwal_id']);
            $bukaJadwal->update(['status_jadwal' => 'booked']);

            DB::commit();

            return redirect()->route('admin.transaksi.booking.index')
                ->with('success', 'Booking berhasil dibuat! Status jadwal otomatis diubah menjadi "booked".');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating booking: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Terjadi kesalahan saat membuat booking.')
                ->withInput();
        }
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'bukajadwal_id' => 'required|exists:buka_jadwal,id',
            'tgl_booking' => 'required|date',
            'catering_id' => 'nullable|exists:catering,id',
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
            // Update expired date jika tanggal booking berubah
            if ($booking->tgl_booking != $validated['tgl_booking']) {
                $validated['tgl_expired_booking'] = Carbon::parse($validated['tgl_booking'])->addWeeks(2);
            }

            // Check jika jadwal berubah
            if ($booking->bukajadwal_id != $validated['bukajadwal_id']) {
                // Check: Jadwal baru sudah ada booking aktif?
                $existingBooking = Booking::where('bukajadwal_id', $validated['bukajadwal_id'])
                    ->where('status_booking', 'active')
                    ->where('id', '!=', $booking->id)
                    ->exists();

                if ($existingBooking) {
                    DB::rollBack();
                    return back()
                        ->with('error', 'Jadwal baru sudah dibooking!')
                        ->withInput();
                }

                // LOGIC: Jadwal lama - cek apakah masih ada booking aktif lain
                $oldJadwal = BukaJadwal::find($booking->bukajadwal_id);
                $hasOtherBooking = Booking::where('bukajadwal_id', $booking->bukajadwal_id)
                    ->where('status_booking', 'active')
                    ->where('id', '!=', $booking->id)
                    ->exists();
                
                // Jika TIDAK ada booking aktif lain, jadwal lama jadi available
                if (!$hasOtherBooking) {
                    $oldJadwal->update(['status_jadwal' => 'available']);
                }

                // LOGIC: Jadwal baru - jika booking baru active, jadwal jadi booked
                if ($validated['status_booking'] === 'active') {
                    $newJadwal = BukaJadwal::find($validated['bukajadwal_id']);
                    $newJadwal->update(['status_jadwal' => 'booked']);
                }
            }

            // Update booking
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

    public function destroy(Booking $booking)
    {
        DB::beginTransaction();
        try {
            // Check if has payment
            $paymentCount = $booking->pembayaran()->count();
            
            if ($paymentCount > 0) {
                return redirect()->route('admin.transaksi.booking.index')
                    ->with('error', "Booking tidak dapat dihapus karena sudah memiliki {$paymentCount} pembayaran!");
            }

            $jadwalId = $booking->bukajadwal_id;
            $userName = $booking->user->nama ?? 'User';
            
            // Delete booking
            $booking->delete();

            // LOGIC: Cek apakah masih ada booking aktif lain di jadwal ini
            $hasOtherBooking = Booking::where('bukajadwal_id', $jadwalId)
                ->where('status_booking', 'active')
                ->exists();
            
            // Jika TIDAK ada booking aktif lain, jadwal jadi available
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

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status_booking' => 'required|in:active,inactive'
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $booking->status_booking;
            $newStatus = $validated['status_booking'];

            // Update booking status
            $booking->update(['status_booking' => $newStatus]);

            $bukaJadwal = $booking->bukaJadwal;

            // LOGIC: Inactive → Available (jika tidak ada booking aktif lain)
            if ($newStatus === 'inactive') {
                $hasOtherActiveBooking = Booking::where('bukajadwal_id', $booking->bukajadwal_id)
                    ->where('status_booking', 'active')
                    ->where('id', '!=', $booking->id)
                    ->exists();
                
                // Jika TIDAK ada booking aktif lain, jadwal jadi available
                if (!$hasOtherActiveBooking) {
                    $bukaJadwal->update(['status_jadwal' => 'available']);
                }
            }
            
            // LOGIC: Active → Booked (check dulu apakah jadwal available)
            if ($newStatus === 'active') {
                // Check: Ada booking aktif lain?
                $hasOtherActiveBooking = Booking::where('bukajadwal_id', $booking->bukajadwal_id)
                    ->where('status_booking', 'active')
                    ->where('id', '!=', $booking->id)
                    ->exists();
                
                if ($hasOtherActiveBooking) {
                    DB::rollBack();
                    return redirect()->route('admin.transaksi.booking.index')
                        ->with('error', 'Tidak dapat mengaktifkan booking karena jadwal sudah dibooking!');
                }
                
                // Jadwal jadi booked
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