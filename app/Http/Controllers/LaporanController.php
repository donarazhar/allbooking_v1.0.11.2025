<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Pembayaran;
use App\Models\BukaJadwal;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        return view('laporan.index');
    }

    public function booking(Request $request)
    {
        $query = Booking::with(['user', 'bukaJadwal.sesi', 'bukaJadwal.jenisAcara', 'catering']);

        if ($request->filled('tanggal_mulai')) {
            $query->where('tgl_booking', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->where('tgl_booking', '<=', $request->tanggal_akhir);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('tgl_booking', 'desc')->get();

        $total = $bookings->count();
        $aktif = $bookings->where('status', 'aktif')->count();
        $tidak_aktif = $bookings->where('status', 'tidak aktif')->count();

        return view('laporan.booking', compact('bookings', 'total', 'aktif', 'tidak_aktif'));
    }

    public function pembayaran(Request $request)
    {
        $query = Pembayaran::with(['booking.user', 'booking.bukaJadwal']);

        if ($request->filled('tanggal_mulai')) {
            $query->where('tgl_pembayaran', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->where('tgl_pembayaran', '<=', $request->tanggal_akhir);
        }
        if ($request->filled('jenis_bayar')) {
            $query->where('jenis_bayar', $request->jenis_bayar);
        }

        $pembayaran = $query->orderBy('tgl_pembayaran', 'desc')->get();

        $total = $pembayaran->count();
        $dp = $pembayaran->where('jenis_bayar', 'DP')->count();
        $termin = $pembayaran->whereIn('jenis_bayar', ['Termin 1', 'Termin 2', 'Termin 3'])->count();
        $pelunasan = $pembayaran->where('jenis_bayar', 'Pelunasan')->count();

        return view('laporan.pembayaran', compact('pembayaran', 'total', 'dp', 'termin', 'pelunasan'));
    }

    public function bukaJadwal(Request $request)
    {
        $query = BukaJadwal::with(['sesi', 'jenisAcara']);

        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_akhir')) {
            $query->where('tanggal', '<=', $request->tanggal_akhir);
        }

        $bukaJadwal = $query->orderBy('tanggal', 'desc')->get();

        $total = $bukaJadwal->count();

        return view('laporan.buka-jadwal', compact('bukaJadwal', 'total'));
    }
}
