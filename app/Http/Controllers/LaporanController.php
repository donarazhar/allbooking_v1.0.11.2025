<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TransaksiBooking;
use App\Models\TransaksiPembayaran;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    /**
     * Laporan Pengguna - Admin (Super Admin & Admin Cabang)
     */
    public function penggunaAdmin(Request $request)
    {
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->role->kode === 'SUPERADMIN';

        // Query builder - Exclude Super Admin, Admin Cabang, dan Pimpinan
        $query = User::with([
            'transaksiBooking.transaksiPembayaran',
            'transaksiBooking.bukaJadwal.jenisAcara',
            'transaksiBooking.bukaJadwal.sesi',
            'transaksiBooking.cabang'
        ])
            ->whereHas('role', function ($q) {
                // ✅ Hanya tampilkan user biasa (bukan Super Admin, Admin, Pimpinan)
                $q->whereNotIn('kode', ['SUPERADMIN', 'ADMIN', 'PIMPINAN']);
            });

        // Filter by cabang for Admin Cabang
        if (!$isSuperAdmin) {
            // Admin Cabang: hanya user yang pernah booking di cabang mereka
            $query->whereHas('transaksiBooking', function ($q) use ($currentUser) {
                $q->where('cabang_id', $currentUser->cabang_id);
            });
        } else {
            // Super Admin: optional filter by cabang
            if ($request->filled('cabang_id')) {
                $query->whereHas('transaksiBooking', function ($q) use ($request) {
                    $q->where('cabang_id', $request->cabang_id);
                });
            }
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereHas('transaksiBooking', function ($q) use ($request) {
                $q->whereDate('tgl_booking', '>=', $request->start_date);
            });
        }

        if ($request->filled('end_date')) {
            $query->whereHas('transaksiBooking', function ($q) use ($request) {
                $q->whereDate('tgl_booking', '<=', $request->end_date);
            });
        }

        // Withcount booking
        if (!$isSuperAdmin) {
            $query->withCount([
                'transaksiBooking' => function ($q) use ($currentUser) {
                    $q->where('cabang_id', $currentUser->cabang_id);
                }
            ]);
        } else {
            $query->withCount('transaksiBooking');
        }

        $users = $query->latest()->get();

        // Stats
        $totalUsers = $users->count();

        if (!$isSuperAdmin) {
            $totalBookings = TransaksiBooking::where('cabang_id', $currentUser->cabang_id)->count();
        } else {
            $totalBookings = TransaksiBooking::when($request->filled('cabang_id'), function ($q) use ($request) {
                return $q->where('cabang_id', $request->cabang_id);
            })->count();
        }

        $activeUsers = $users->filter(function ($user) {
            return $user->transaksi_booking_count > 0;
        })->count();

        // Get cabang list for filter (Super Admin only)
        $cabangList = $isSuperAdmin ? Cabang::orderBy('nama')->get() : collect();

        return view('admin.laporan.pengguna', compact(
            'users',
            'totalUsers',
            'totalBookings',
            'activeUsers',
            'cabangList',
            'isSuperAdmin'
        ));
    }

    /**
     * Laporan Keuangan - Admin (Super Admin & Admin Cabang)
     */
    public function keuanganAdmin(Request $request)
    {
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->role->kode === 'SUPERADMIN';

        // Query builder
        $query = TransaksiPembayaran::with([
            'transaksiBooking.user',
            'transaksiBooking.bukaJadwal.jenisAcara',
            'transaksiBooking.bukaJadwal.sesi',
            'cabang'
        ]);

        // Filter by cabang for Admin Cabang
        if (!$isSuperAdmin) {
            $query->where('cabang_id', $currentUser->cabang_id);
        } else {
            // Super Admin: optional filter by cabang
            if ($request->filled('cabang_id')) {
                $query->where('cabang_id', $request->cabang_id);
            }
        }

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

        // Calculate totals
        $totalPembayaran = $pembayarans->sum('nominal');
        $totalDP = $pembayarans->where('jenis_bayar', 'DP')->sum('nominal');
        $totalTermin = $pembayarans->filter(function ($p) {
            return str_contains($p->jenis_bayar, 'Termin');
        })->sum('nominal');
        $totalPelunasan = $pembayarans->where('jenis_bayar', 'Pelunasan')->sum('nominal');

        // Monthly revenue
        $monthlyRevenue = $pembayarans->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->tgl_pembayaran)->format('Y-m');
        })->map(function ($group) {
            return $group->sum('nominal');
        });

        // Get cabang list for filter (Super Admin only)
        $cabangList = $isSuperAdmin ? Cabang::orderBy('nama')->get() : collect();

        return view('admin.laporan.keuangan', compact(
            'pembayarans',
            'totalPembayaran',
            'totalDP',
            'totalTermin',
            'totalPelunasan',
            'monthlyRevenue',
            'cabangList',
            'isSuperAdmin'
        ));
    }

    /**
     * Laporan Pengguna - Pimpinan
     */
    public function penggunaPimpinan(Request $request)
    {
        $currentUser = Auth::user();
        $cabangId = $currentUser->cabang_id;
        $cabangInfo = Cabang::find($cabangId);

        // Query builder - Filter by cabang pimpinan
        $query = User::with([
            'transaksiBooking.transaksiPembayaran',
            'transaksiBooking.bukaJadwal.jenisAcara',
            'transaksiBooking.bukaJadwal.sesi',
            'transaksiBooking.cabang'
        ])
            ->whereHas('role', function ($q) {
                // Hanya tampilkan user biasa (bukan Super Admin, Admin, Pimpinan)
                $q->whereNotIn('kode', ['SUPERADMIN', 'ADMIN', 'PIMPINAN']);
            })
            ->whereHas('transaksiBooking', function ($q) use ($cabangId) {
                $q->where('cabang_id', $cabangId);
            });

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereHas('transaksiBooking', function ($q) use ($request) {
                $q->whereDate('tgl_booking', '>=', $request->start_date);
            });
        }

        if ($request->filled('end_date')) {
            $query->whereHas('transaksiBooking', function ($q) use ($request) {
                $q->whereDate('tgl_booking', '<=', $request->end_date);
            });
        }

        // Withcount booking untuk cabang pimpinan saja
        $query->withCount([
            'transaksiBooking' => function ($q) use ($cabangId) {
                $q->where('cabang_id', $cabangId);
            }
        ]);

        $users = $query->latest()->get();

        // Stats
        $totalUsers = $users->count();
        $totalBookings = TransaksiBooking::where('cabang_id', $cabangId)->count();

        $activeUsers = $users->filter(function ($user) {
            return $user->transaksi_booking_count > 0;
        })->count();

        return view('pimpinan.laporan.pengguna', compact(
            'users',
            'totalUsers',
            'totalBookings',
            'activeUsers',
            'cabangInfo'
        ));
    }
    /**
     * Laporan Keuangan - Pimpinan
     */
    public function keuanganPimpinan(Request $request)
    {
        // Query builder
        $query = TransaksiPembayaran::with([
            'transaksiBooking.user',
            'transaksiBooking.bukaJadwal.jenisAcara',
            'transaksiBooking.bukaJadwal.sesi',
            'cabang'
        ]);

        // Filter by cabang
        if ($request->filled('cabang_id')) {
            $query->where('cabang_id', $request->cabang_id);
        }

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

        // Calculate totals
        $totalPembayaran = $pembayarans->sum('nominal');
        $totalDP = $pembayarans->where('jenis_bayar', 'DP')->sum('nominal');
        $totalTermin = $pembayarans->filter(function ($p) {
            return str_contains($p->jenis_bayar, 'Termin');
        })->sum('nominal');
        $totalPelunasan = $pembayarans->where('jenis_bayar', 'Pelunasan')->sum('nominal');

        // Monthly revenue
        $monthlyRevenue = $pembayarans->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->tgl_pembayaran)->format('Y-m');
        })->map(function ($group) {
            return $group->sum('nominal');
        });

        // Get cabang list for filter
        $cabangList = Cabang::orderBy('nama')->get();

        return view('pimpinan.laporan.keuangan', compact(
            'pembayarans',
            'totalPembayaran',
            'totalDP',
            'totalTermin',
            'totalPelunasan',
            'monthlyRevenue',
            'cabangList'
        ));
    }
}
