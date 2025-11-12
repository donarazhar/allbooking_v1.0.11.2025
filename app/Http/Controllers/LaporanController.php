<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Laporan Pengguna - Data user dan booking
     */
    public function penggunaAdmin(Request $request)
    {
        // Same logic as pengguna()
        $query = User::with(['bookings.pembayaran', 'bookings.bukaJadwal'])
            ->where('role_id', '!=', 1)
            ->withCount('bookings');

        if ($request->filled('start_date')) {
            $query->whereHas('bookings', function ($q) use ($request) {
                $q->whereDate('tgl_booking', '>=', $request->start_date);
            });
        }

        if ($request->filled('end_date')) {
            $query->whereHas('bookings', function ($q) use ($request) {
                $q->whereDate('tgl_booking', '<=', $request->end_date);
            });
        }

        $users = $query->latest()->get();

        $totalUsers = $users->count();
        $totalBookings = Booking::count();
        $activeUsers = $users->filter(function ($user) {
            return $user->bookings_count > 0;
        })->count();

        // Use admin layout
        return view('admin.laporan.pengguna', compact(
            'users',
            'totalUsers',
            'totalBookings',
            'activeUsers'
        ));
    }

    public function keuanganAdmin(Request $request)
    {
        $query = Pembayaran::with(['bookings.user', 'bookings.bukaJadwal']);

        if ($request->filled('start_date')) {
            $query->whereDate('tgl_pembayaran', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tgl_pembayaran', '<=', $request->end_date);
        }

        if ($request->filled('jenis_bayar')) {
            $query->where('jenis_bayar', $request->jenis_bayar);
        }

        $pembayarans = $query->latest('tgl_pembayaran')->get();

        $totalPembayaran = $pembayarans->sum('nominal');
        $totalDP = $pembayarans->where('jenis_bayar', 'DP')->sum('nominal');
        $totalTermin = $pembayarans->filter(function ($p) {
            return str_contains($p->jenis_bayar, 'Termin');
        })->sum('nominal');
        $totalPelunasan = $pembayarans->where('jenis_bayar', 'Pelunasan')->sum('nominal');

        $monthlyRevenue = $pembayarans->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->tgl_pembayaran)->format('Y-m');
        })->map(function ($group) {
            return $group->sum('nominal');
        });

        // Use admin layout
        return view('admin.laporan.keuangan', compact(
            'pembayarans',
            'totalPembayaran',
            'totalDP',
            'totalTermin',
            'totalPelunasan',
            'monthlyRevenue'
        ));
    }

    public function penggunaPimpinan(Request $request)
    {
        // Same as penggunaAdmin but use pimpinan layout
        $query = User::with(['bookings.pembayaran', 'bookings.bukaJadwal'])
            ->where('role_id', '!=', 1)
            ->withCount('bookings');

        if ($request->filled('start_date')) {
            $query->whereHas('bookings', function ($q) use ($request) {
                $q->whereDate('tgl_booking', '>=', $request->start_date);
            });
        }

        if ($request->filled('end_date')) {
            $query->whereHas('bookings', function ($q) use ($request) {
                $q->whereDate('tgl_booking', '<=', $request->end_date);
            });
        }

        $users = $query->latest()->get();

        $totalUsers = $users->count();
        $totalBookings = Booking::count();
        $activeUsers = $users->filter(function ($user) {
            return $user->bookings_count > 0;
        })->count();

        // Use pimpinan layout
        return view('pimpinan.laporan.pengguna', compact(
            'users',
            'totalUsers',
            'totalBookings',
            'activeUsers'
        ));
    }

    public function keuanganPimpinan(Request $request)
    {
        // Same as keuanganAdmin but use pimpinan layout
        $query = Pembayaran::with(['bookings.user', 'bookings.bukaJadwal']);

        if ($request->filled('start_date')) {
            $query->whereDate('tgl_pembayaran', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('tgl_pembayaran', '<=', $request->end_date);
        }

        if ($request->filled('jenis_bayar')) {
            $query->where('jenis_bayar', $request->jenis_bayar);
        }

        $pembayarans = $query->latest('tgl_pembayaran')->get();

        $totalPembayaran = $pembayarans->sum('nominal');
        $totalDP = $pembayarans->where('jenis_bayar', 'DP')->sum('nominal');
        $totalTermin = $pembayarans->filter(function ($p) {
            return str_contains($p->jenis_bayar, 'Termin');
        })->sum('nominal');
        $totalPelunasan = $pembayarans->where('jenis_bayar', 'Pelunasan')->sum('nominal');

        $monthlyRevenue = $pembayarans->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->tgl_pembayaran)->format('Y-m');
        })->map(function ($group) {
            return $group->sum('nominal');
        });

        // Use pimpinan layout
        return view('pimpinan.laporan.keuangan', compact(
            'pembayarans',
            'totalPembayaran',
            'totalDP',
            'totalTermin',
            'totalPelunasan',
            'monthlyRevenue'
        ));
    }
}
