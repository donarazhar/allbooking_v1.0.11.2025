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
    public function pengguna(Request $request)
    {
        $query = User::with(['bookings.pembayaran', 'bookings.bukaJadwal'])
            ->where('role_id', '!=', 1) // Exclude admin
            ->withCount('bookings');
        
        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereHas('bookings', function($q) use ($request) {
                $q->whereDate('tgl_booking', '>=', $request->start_date);
            });
        }
        
        if ($request->filled('end_date')) {
            $query->whereHas('bookings', function($q) use ($request) {
                $q->whereDate('tgl_booking', '<=', $request->end_date);
            });
        }
        
        $users = $query->latest()->get();
        
        // Calculate stats
        $totalUsers = $users->count();
        $totalBookings = Booking::count();
        $activeUsers = $users->filter(function($user) {
            return $user->bookings_count > 0;
        })->count();
        
        return view('pimpinan.laporan.pengguna', compact(
            'users',
            'totalUsers',
            'totalBookings',
            'activeUsers'
        ));
    }
    
    /**
     * Laporan Keuangan - Data pembayaran
     */
    public function keuangan(Request $request)
    {
        $query = Pembayaran::with(['bookings.user', 'bookings.bukaJadwal']);
        
        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('tgl_pembayaran', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('tgl_pembayaran', '<=', $request->end_date);
        }
        
        // Filter by jenis bayar
        if ($request->filled('jenis_bayar')) {
            $query->where('jenis_bayar', $request->jenis_bayar);
        }
        
        $pembayarans = $query->latest('tgl_pembayaran')->get();
        
        // Calculate stats
        $totalPembayaran = $pembayarans->sum('nominal');
        $totalDP = $pembayarans->where('jenis_bayar', 'DP')->sum('nominal');
        $totalTermin = $pembayarans->filter(function($p) {
            return str_contains($p->jenis_bayar, 'Termin');
        })->sum('nominal');
        $totalPelunasan = $pembayarans->where('jenis_bayar', 'Pelunasan')->sum('nominal');
        
        // Monthly revenue
        $monthlyRevenue = $pembayarans->groupBy(function($item) {
            return \Carbon\Carbon::parse($item->tgl_pembayaran)->format('Y-m');
        })->map(function($group) {
            return $group->sum('nominal');
        });
        
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