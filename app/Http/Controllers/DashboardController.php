<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BukaJadwal;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Periode filter (default: bulan ini)
        $startDate = request('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Stats Cards
        $stats = [
            'total_users' => User::where('role_id', '3')->count(),
            'users_this_month' => User::where('role_id', '3')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'users_last_month' => User::where('role_id', '3')
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->count(),

            'total_bookings' => Booking::count(),
            'bookings_this_month' => Booking::whereBetween('tgl_booking', [$startDate, $endDate])->count(),
            'bookings_last_month' => Booking::whereBetween('tgl_booking', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])->count(),

            'active_bookings' => Booking::where('status_booking', 'active')->count(),
            'inactive_bookings' => Booking::where('status_booking', 'inactive')->count(),

            'total_schedules' => BukaJadwal::count(),
            'schedules_this_month' => BukaJadwal::whereBetween('tanggal', [$startDate, $endDate])->count(),
        ];

        // Calculate percentage changes
        $stats['users_growth'] = $stats['users_last_month'] > 0
            ? round((($stats['users_this_month'] - $stats['users_last_month']) / $stats['users_last_month']) * 100, 1)
            : 0;

        $stats['bookings_growth'] = $stats['bookings_last_month'] > 0
            ? round((($stats['bookings_this_month'] - $stats['bookings_last_month']) / $stats['bookings_last_month']) * 100, 1)
            : 0;

        // Recent Bookings (10 terbaru)
        $recentBookings = Booking::with(['user', 'bukaJadwal.jenisAcara', 'bukaJadwal.sesi', 'catering'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Booking Trend (7 hari terakhir)
        $bookingTrend = Booking::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Fill missing dates with 0
        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $trendData[$date] = $bookingTrend[$date] ?? 0;
        }

        // Booking by Status
        $bookingsByStatus = [
            'active' => $stats['active_bookings'],
            'inactive' => $stats['inactive_bookings'],
        ];

        // Booking by Jenis Acara
        $bookingsByJenis = Booking::join('buka_jadwal', 'transaksi_booking.bukajadwal_id', '=', 'buka_jadwal.id')
            ->join('jenis_acara', 'buka_jadwal.jenisacara_id', '=', 'jenis_acara.id')
            ->select('jenis_acara.nama', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_acara.nama')
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('total', 'nama')
            ->toArray();

        // Pending Actions
        $pendingActions = [
            'new_bookings' => Booking::where('status_booking', 'inactive')->count(),
            'expired_soon' => Booking::where('status_booking', 'active')
                ->whereNotNull('tgl_expired_booking')
                ->where('tgl_expired_booking', '<=', now()->addDays(3))
                ->count(),
            'no_dp' => Booking::where('status_booking', 'active')
                ->count(),
        ];

        // Upcoming Schedules (5 jadwal terdekat)
        $upcomingSchedules = BukaJadwal::with(['jenisAcara', 'sesi'])
            ->where('tanggal', '>=', now()->format('Y-m-d'))
            ->whereDoesntHave('bookings', function ($query) {
                $query->whereIn('status_booking', ['pending', 'active']);
            })
            ->orderBy('tanggal')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'stats',
            'recentBookings',
            'trendData',
            'bookingsByStatus',
            'bookingsByJenis',
            'pendingActions',
            'upcomingSchedules',
            'startDate',
            'endDate'
        ));
    }

    public function pimpinanDashboard()
    {
        $totalUsers = User::count();
        $totalBookings = Booking::count();
        $totalRevenue = Pembayaran::sum('nominal');

        $activeBookings = Booking::where('status_booking', 'active')->count();
        $pendingBookings = Booking::where('status_booking', 'inactive')->count();

        // Count bookings that have Pelunasan payment
        $lunasBookings = Booking::whereHas('pembayaran', function ($q) {
            $q->where('jenis_bayar', 'Pelunasan');
        })->count();

        $totalPayments = Pembayaran::count();

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
