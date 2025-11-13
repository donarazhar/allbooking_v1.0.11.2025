<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TransaksiBooking;
use App\Models\BukaJadwal;
use App\Models\TransaksiPembayaran;
use App\Models\User;
use App\Models\Role;
use App\Models\Cabang;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard Admin (Super Admin & Admin Cabang)
     */
    public function index()
    {
        $user = Auth::user();

        // Check if user has role
        if (!$user->role) {
            abort(403, 'Role tidak ditemukan.');
        }

        // Periode filter (default: bulan ini)
        $startDate = request('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Determine if Super Admin
        $isSuperAdmin = in_array($user->role->kode, ['SUPERADMIN']);
        $isAdmin = $user->role->kode === 'ADMIN';

        // Filter cabang untuk Admin Cabang
        $cabangId = !$isSuperAdmin ? $user->cabang_id : null;

        // Get cabang info untuk admin cabang
        $cabangInfo = $cabangId ? Cabang::find($cabangId) : null;

        // =====================
        // === STATS SUMMARY ===
        // =====================

        // Get User role ID
        $userRoleId = Role::where('kode', 'USER')->first()?->id;

        // Base queries dengan explicit table names
        $userQuery = User::query();
        if ($userRoleId) {
            $userQuery->where('users.role_id', $userRoleId);
        }

        $bookingQuery = TransaksiBooking::query();
        $bukaJadwalQuery = BukaJadwal::query();
        $pembayaranQuery = TransaksiPembayaran::query();

        // Apply cabang filter dengan explicit table name
        if ($cabangId) {
            $userQuery->where('users.cabang_id', $cabangId);
            $bookingQuery->where('transaksi_booking.cabang_id', $cabangId); // ✅ FIX: Add table prefix
            $bukaJadwalQuery->where('buka_jadwal.cabang_id', $cabangId); // ✅ FIX: Add table prefix
            $pembayaranQuery->where('transaksi_pembayaran.cabang_id', $cabangId); // ✅ FIX: Add table prefix
        }

        // Calculate statistics
        $stats = [
            // Users Statistics
            'total_users' => (clone $userQuery)->count(),
            'users_this_month' => (clone $userQuery)
                ->whereMonth('users.created_at', now()->month)
                ->whereYear('users.created_at', now()->year)
                ->count(),
            'users_last_month' => (clone $userQuery)
                ->whereMonth('users.created_at', now()->subMonth()->month)
                ->whereYear('users.created_at', now()->subMonth()->year)
                ->count(),
            'users_active' => (clone $userQuery)
                ->where('users.status_users', 'active')
                ->count(),
            'users_inactive' => (clone $userQuery)
                ->where('users.status_users', 'inactive')
                ->count(),

            // Bookings Statistics
            'total_bookings' => (clone $bookingQuery)->count(),
            'bookings_this_month' => (clone $bookingQuery)
                ->whereBetween('transaksi_booking.tgl_booking', [$startDate, $endDate])
                ->count(),
            'bookings_last_month' => (clone $bookingQuery)
                ->whereBetween('transaksi_booking.tgl_booking', [
                    now()->subMonth()->startOfMonth(),
                    now()->subMonth()->endOfMonth()
                ])->count(),
            'active_bookings' => (clone $bookingQuery)
                ->where('transaksi_booking.status_booking', 'active')
                ->count(),
            'inactive_bookings' => (clone $bookingQuery)
                ->where('transaksi_booking.status_booking', 'inactive')
                ->count(),
            'expired_bookings' => (clone $bookingQuery)
                ->where('transaksi_booking.status_booking', 'expired')
                ->count(),
            'cancelled_bookings' => (clone $bookingQuery)
                ->where('transaksi_booking.status_booking', 'cancelled')
                ->count(),

            // Schedules Statistics
            'total_schedules' => (clone $bukaJadwalQuery)->count(),
            'available_schedules' => (clone $bukaJadwalQuery)
                ->where('buka_jadwal.status_jadwal', 'available')
                ->count(),
            'booked_schedules' => (clone $bukaJadwalQuery)
                ->where('buka_jadwal.status_jadwal', 'booked')
                ->count(),
            'schedules_this_month' => (clone $bukaJadwalQuery)
                ->whereBetween('buka_jadwal.tanggal', [$startDate, $endDate])
                ->count(),

            // Revenue Statistics
            'total_revenue' => (clone $pembayaranQuery)->sum('transaksi_pembayaran.nominal'),
            'revenue_this_month' => (clone $pembayaranQuery)
                ->whereBetween('transaksi_pembayaran.tgl_pembayaran', [$startDate, $endDate])
                ->sum('transaksi_pembayaran.nominal'),
            'revenue_last_month' => (clone $pembayaranQuery)
                ->whereBetween('transaksi_pembayaran.tgl_pembayaran', [
                    now()->subMonth()->startOfMonth(),
                    now()->subMonth()->endOfMonth()
                ])->sum('transaksi_pembayaran.nominal'),
            'total_payments' => (clone $pembayaranQuery)->count(),
            'payments_this_month' => (clone $pembayaranQuery)
                ->whereBetween('transaksi_pembayaran.tgl_pembayaran', [$startDate, $endDate])
                ->count(),
        ];

        // Calculate growth percentages
        $stats['users_growth'] = $stats['users_last_month'] > 0
            ? round((($stats['users_this_month'] - $stats['users_last_month']) / $stats['users_last_month']) * 100, 1)
            : ($stats['users_this_month'] > 0 ? 100 : 0);

        $stats['bookings_growth'] = $stats['bookings_last_month'] > 0
            ? round((($stats['bookings_this_month'] - $stats['bookings_last_month']) / $stats['bookings_last_month']) * 100, 1)
            : ($stats['bookings_this_month'] > 0 ? 100 : 0);

        $stats['revenue_growth'] = $stats['revenue_last_month'] > 0
            ? round((($stats['revenue_this_month'] - $stats['revenue_last_month']) / $stats['revenue_last_month']) * 100, 1)
            : ($stats['revenue_this_month'] > 0 ? 100 : 0);

        // =====================
        // === RECENT DATA ===
        // =====================
        $recentBookings = (clone $bookingQuery)
            ->with([
                'user:id,nama,email,no_hp',
                'bukaJadwal.jenisAcara:id,nama,harga',
                'bukaJadwal.sesi:id,nama,jam_mulai,jam_selesai',
                'bukaJadwal.cabang:id,nama,kode',
                'catering:id,nama,no_hp'
            ])
            ->orderBy('transaksi_booking.created_at', 'desc')
            ->limit(10)
            ->get();

        $recentUsers = (clone $userQuery)
            ->with(['role:id,nama', 'cabang:id,nama'])
            ->orderBy('users.created_at', 'desc')
            ->limit(5)
            ->get();

        // =====================
        // === TREND CHART (7 Days) ===
        // =====================
        $bookingTrend = (clone $bookingQuery)
            ->select(
                DB::raw('DATE(transaksi_booking.tgl_booking) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('transaksi_booking.tgl_booking', '>=', now()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        $trendData = [];
        $trendLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $trendData[] = $bookingTrend[$date] ?? 0;
            $trendLabels[] = now()->subDays($i)->format('d M');
        }

        // =====================
        // === REVENUE TREND (6 Months) ===
        // =====================
        $revenueTrend = (clone $pembayaranQuery)
            ->select(
                DB::raw('DATE_FORMAT(transaksi_pembayaran.tgl_pembayaran, "%Y-%m") as month'),
                DB::raw('SUM(transaksi_pembayaran.nominal) as total')
            )
            ->where('transaksi_pembayaran.tgl_pembayaran', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $revenueData = [];
        $revenueLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $revenueData[] = $revenueTrend[$month] ?? 0;
            $revenueLabels[] = now()->subMonths($i)->format('M Y');
        }

        // =====================
        // === BOOKING STATUS ===
        // =====================
        $bookingsByStatus = [
            'Active' => $stats['active_bookings'],
            'Inactive' => $stats['inactive_bookings'],
            'Expired' => $stats['expired_bookings'],
            'Cancelled' => $stats['cancelled_bookings'],
        ];

        // =====================
        // === BOOKING BY JENIS ACARA ===
        // ✅ FIX: Specify table name in WHERE clause
        // =====================
        $bookingsByJenisQuery = TransaksiBooking::query()
            ->join('buka_jadwal', 'transaksi_booking.bukajadwal_id', '=', 'buka_jadwal.id')
            ->join('jenis_acara', 'buka_jadwal.jenisacara_id', '=', 'jenis_acara.id')
            ->select('jenis_acara.nama', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_acara.id', 'jenis_acara.nama')
            ->orderBy('total', 'desc')
            ->limit(5);

        // Apply cabang filter with explicit table name
        if ($cabangId) {
            $bookingsByJenisQuery->where('transaksi_booking.cabang_id', $cabangId); // ✅ FIX
        }

        $bookingsByJenis = $bookingsByJenisQuery
            ->get()
            ->pluck('total', 'nama')
            ->toArray();

        // =====================
        // === PENDING ACTIONS ===
        // =====================
        $pendingActions = [
            'new_users' => (clone $userQuery)
                ->where('users.status_users', 'inactive')
                ->count(),
            'new_bookings' => (clone $bookingQuery)
                ->where('transaksi_booking.status_booking', 'inactive')
                ->count(),
            'expired_soon' => (clone $bookingQuery)
                ->where('transaksi_booking.status_booking', 'active')
                ->whereNotNull('transaksi_booking.tgl_expired_booking')
                ->whereBetween('transaksi_booking.tgl_expired_booking', [now(), now()->addDays(3)])
                ->count(),
            'unpaid_bookings' => (clone $bookingQuery)
                ->where('transaksi_booking.status_booking', 'active')
                ->whereDoesntHave('transaksiPembayaran')
                ->count(),
        ];

        // =====================
        // === UPCOMING SCHEDULES ===
        // =====================
        $upcomingSchedules = (clone $bukaJadwalQuery)
            ->with([
                'jenisAcara:id,nama,harga',
                'sesi:id,nama,jam_mulai,jam_selesai',
                'cabang:id,nama,kode'
            ])
            ->where('buka_jadwal.tanggal', '>=', now()->format('Y-m-d'))
            ->where('buka_jadwal.status_jadwal', 'available')
            ->orderBy('buka_jadwal.tanggal')
            ->orderBy('sesi_id')
            ->limit(5)
            ->get();

        // =====================
        // === PAYMENT BY TYPE ===
        // =====================
        $paymentsByType = (clone $pembayaranQuery)
            ->select('transaksi_pembayaran.jenis_bayar', DB::raw('COUNT(*) as total'), DB::raw('SUM(transaksi_pembayaran.nominal) as amount'))
            ->groupBy('transaksi_pembayaran.jenis_bayar')
            ->get()
            ->keyBy('jenis_bayar');

        // =====================
        // === CABANG STATISTICS (Super Admin Only) ===
        // =====================
        $cabangStats = [];
        if ($isSuperAdmin) {
            $cabangStats = Cabang::withCount([
                'transaksiBooking',
                'transaksiBooking as active_bookings' => function ($q) {
                    $q->where('status_booking', 'active');
                }
            ])
                ->withSum('transaksiPembayaran', 'nominal')
                ->get();
        }

        return view('admin.dashboard', compact(
            'stats',
            'recentBookings',
            'recentUsers',
            'trendData',
            'trendLabels',
            'revenueData',
            'revenueLabels',
            'bookingsByStatus',
            'bookingsByJenis',
            'pendingActions',
            'upcomingSchedules',
            'paymentsByType',
            'cabangStats',
            'startDate',
            'endDate',
            'isSuperAdmin',
            'isAdmin',
            'cabangInfo'
        ));
    }

    /**
     * Dashboard Pimpinan
     */
    public function pimpinanDashboard()
    {
        $user = Auth::user();

        // Check if user has role
        if (!$user->role) {
            abort(403, 'Role tidak ditemukan.');
        }

        // Filter berdasarkan cabang pimpinan
        $cabangId = $user->cabang_id;
        $cabangInfo = Cabang::find($cabangId);

        // Periode filter (default: bulan ini)
        $startDate = request('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = request('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Base queries dengan explicit table names dan filter cabang
        $userQuery = User::where('users.cabang_id', $cabangId);
        $bookingQuery = TransaksiBooking::where('transaksi_booking.cabang_id', $cabangId);
        $pembayaranQuery = TransaksiPembayaran::where('transaksi_pembayaran.cabang_id', $cabangId);

        // Statistics
        $stats = [
            // Users
            'total_users' => (clone $userQuery)->count(),
            'users_active' => (clone $userQuery)->where('users.status_users', 'active')->count(),
            'users_inactive' => (clone $userQuery)->where('users.status_users', 'inactive')->count(),

            // Bookings
            'total_bookings' => (clone $bookingQuery)->count(),
            'active_bookings' => (clone $bookingQuery)->where('transaksi_booking.status_booking', 'active')->count(),
            'inactive_bookings' => (clone $bookingQuery)->where('transaksi_booking.status_booking', 'inactive')->count(),
            'bookings_this_month' => (clone $bookingQuery)
                ->whereBetween('transaksi_booking.tgl_booking', [$startDate, $endDate])
                ->count(),

            // Revenue
            'total_revenue' => (clone $pembayaranQuery)->sum('transaksi_pembayaran.nominal'),
            'revenue_this_month' => (clone $pembayaranQuery)
                ->whereBetween('transaksi_pembayaran.tgl_pembayaran', [$startDate, $endDate])
                ->sum('transaksi_pembayaran.nominal'),
            'total_payments' => (clone $pembayaranQuery)->count(),

            // Paid bookings (yang sudah lunas)
            'paid_bookings' => (clone $bookingQuery)
                ->whereHas('transaksiPembayaran', function ($q) {
                    $q->where('jenis_bayar', 'Pelunasan');
                })
                ->count(),
        ];

        // Recent bookings
        $recentBookings = (clone $bookingQuery)
            ->with([
                'user:id,nama,email',
                'bukaJadwal.jenisAcara:id,nama,harga',
                'bukaJadwal.sesi:id,nama,jam_mulai,jam_selesai',
                'catering:id,nama'
            ])
            ->orderBy('transaksi_booking.created_at', 'desc')
            ->limit(10)
            ->get();

        // Revenue trend (6 months)
        $revenueTrend = (clone $pembayaranQuery)
            ->select(
                DB::raw('DATE_FORMAT(transaksi_pembayaran.tgl_pembayaran, "%Y-%m") as month'),
                DB::raw('SUM(transaksi_pembayaran.nominal) as total')
            )
            ->where('transaksi_pembayaran.tgl_pembayaran', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        $revenueData = [];
        $revenueLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $revenueData[] = $revenueTrend[$month] ?? 0;
            $revenueLabels[] = now()->subMonths($i)->format('M Y');
        }

        // Bookings by jenis acara - ✅ FIX with explicit table name
        $bookingsByJenis = TransaksiBooking::query()
            ->where('transaksi_booking.cabang_id', $cabangId) // ✅ FIX
            ->join('buka_jadwal', 'transaksi_booking.bukajadwal_id', '=', 'buka_jadwal.id')
            ->join('jenis_acara', 'buka_jadwal.jenisacara_id', '=', 'jenis_acara.id')
            ->select('jenis_acara.nama', DB::raw('COUNT(*) as total'))
            ->groupBy('jenis_acara.id', 'jenis_acara.nama')
            ->orderBy('total', 'desc')
            ->get()
            ->pluck('total', 'nama')
            ->toArray();

        return view('pimpinan.dashboard', compact(
            'stats',
            'recentBookings',
            'revenueData',
            'revenueLabels',
            'bookingsByJenis',
            'cabangInfo',
            'startDate',
            'endDate'
        ));
    }
}
