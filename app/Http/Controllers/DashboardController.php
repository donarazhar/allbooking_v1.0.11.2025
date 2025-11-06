<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

   public function pimpinanDashboard()
    {
        $totalUsers = \App\Models\User::count();
        $totalBookings = \App\Models\Booking::count();
        $totalRevenue = \App\Models\Pembayaran::sum('nominal');

        $activeBookings = \App\Models\Booking::where('status_booking', 'active')->count();
        $pendingBookings = \App\Models\Booking::where('status_booking', 'inactive')->count();

        // Count bookings that have Pelunasan payment
        $lunasBookings = \App\Models\Booking::whereHas('pembayaran', function ($q) {
            $q->where('jenis_bayar', 'Pelunasan');
        })->count();

        $totalPayments = \App\Models\Pembayaran::count();

        return view('pimpinan.dashboard', compact(
            'totalUsers',
            'totalBookings',
            'totalRevenue',
            'activeBookings',
            'pendingBookings',
            'lunasBookings',
            'totalPayments'
        ));
    }
}
