<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPembayaran;
use App\Models\TransaksiBooking;
use App\Models\Cabang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $isSuperAdmin = $currentUser->role->kode === 'SUPERADMIN';

        // Query builder
        $query = TransaksiPembayaran::with([
            'transaksiBooking.user',
            'transaksiBooking.bukaJadwal.sesi',
            'transaksiBooking.bukaJadwal.jenisAcara',
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

        $pembayaran = $query->orderBy('tgl_pembayaran', 'desc')->get();

        // Get cabang list for filter (Super Admin only)
        $cabangList = $isSuperAdmin ? Cabang::orderBy('nama')->get() : collect();

        return view('admin.transaksi.pembayaran.index', compact('pembayaran', 'cabangList', 'isSuperAdmin'));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can create
        if ($currentUser->role->kode === 'SUPERADMIN') {
            return back()->with('error', 'Super Admin tidak dapat menambah pembayaran. Fitur ini khusus untuk Admin Cabang.');
        }

        $validated = $request->validate([
            'tgl_pembayaran' => 'required|date',
            'booking_id' => 'required|exists:transaksi_booking,id',
            'jenis_bayar' => 'required|in:DP,Termin 1,Termin 2,Termin 3,Termin 4,Pelunasan',
            'nominal' => 'required|numeric|min:1000|max:999999999',
            'bukti_bayar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'tgl_pembayaran.required' => 'Tanggal pembayaran harus diisi',
            'booking_id.required' => 'Booking harus dipilih',
            'booking_id.exists' => 'Booking tidak ditemukan',
            'jenis_bayar.required' => 'Jenis bayar harus dipilih',
            'jenis_bayar.in' => 'Jenis bayar tidak valid',
            'nominal.required' => 'Nominal pembayaran harus diisi',
            'nominal.numeric' => 'Nominal harus berupa angka',
            'nominal.min' => 'Nominal minimal Rp 1.000',
            'nominal.max' => 'Nominal maksimal Rp 999.999.999',
            'bukti_bayar.image' => 'Bukti bayar harus berupa gambar',
            'bukti_bayar.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'bukti_bayar.max' => 'Ukuran bukti bayar maksimal 2MB'
        ]);

        // Verify booking belongs to admin's cabang
        $booking = TransaksiBooking::where('id', $validated['booking_id'])
            ->where('cabang_id', $currentUser->cabang_id)
            ->first();

        if (!$booking) {
            return back()->with('error', 'Booking tidak ditemukan atau bukan milik cabang Anda!');
        }

        DB::beginTransaction();
        try {
            // Upload bukti bayar jika ada
            if ($request->hasFile('bukti_bayar')) {
                $uploadPath = public_path('uploads/bukti_bayar');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file = $request->file('bukti_bayar');
                $filename = 'bayar_' . $booking->id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                $validated['bukti_bayar'] = $filename;
            }

            // Add cabang_id
            $validated['cabang_id'] = $currentUser->cabang_id;

            // Create pembayaran
            TransaksiPembayaran::create($validated);

            // If DP, activate booking
            if ($validated['jenis_bayar'] === 'DP') {
                $booking->update([
                    'status_booking' => 'active',
                    'tgl_expired_booking' => null
                ]);
            }

            DB::commit();

            return redirect()->route('admin.transaksi.pembayaran.index')
                ->with('success', 'Pembayaran berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Cleanup uploaded file jika ada error
            if (isset($validated['bukti_bayar']) && file_exists(public_path('uploads/bukti_bayar/' . $validated['bukti_bayar']))) {
                unlink(public_path('uploads/bukti_bayar/' . $validated['bukti_bayar']));
            }

            Log::error('Error creating pembayaran: ' . $e->getMessage());

            return back()
                ->with('error', 'Terjadi kesalahan saat menambahkan pembayaran.')
                ->withInput();
        }
    }

    public function update(Request $request, TransaksiPembayaran $pembayaran)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can update
        if ($currentUser->role->kode === 'SUPERADMIN') {
            return back()->with('error', 'Super Admin tidak dapat mengupdate pembayaran. Fitur ini khusus untuk Admin Cabang.');
        }

        // Verify pembayaran belongs to admin's cabang
        if ($pembayaran->cabang_id !== $currentUser->cabang_id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengupdate pembayaran ini!');
        }

        $validated = $request->validate([
            'tgl_pembayaran' => 'required|date',
            'booking_id' => 'required|exists:transaksi_booking,id',
            'jenis_bayar' => 'required|in:DP,Termin 1,Termin 2,Termin 3,Termin 4,Pelunasan',
            'nominal' => 'required|numeric|min:1000|max:999999999',
            'bukti_bayar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'tgl_pembayaran.required' => 'Tanggal pembayaran harus diisi',
            'booking_id.required' => 'Booking harus dipilih',
            'jenis_bayar.required' => 'Jenis bayar harus dipilih',
            'nominal.required' => 'Nominal pembayaran harus diisi',
            'nominal.min' => 'Nominal minimal Rp 1.000',
            'nominal.max' => 'Nominal maksimal Rp 999.999.999',
            'bukti_bayar.image' => 'Bukti bayar harus berupa gambar',
            'bukti_bayar.max' => 'Ukuran bukti bayar maksimal 2MB'
        ]);

        DB::beginTransaction();
        try {
            // Handle upload bukti bayar baru
            if ($request->hasFile('bukti_bayar')) {
                // Hapus bukti lama jika ada
                if ($pembayaran->bukti_bayar && file_exists(public_path('uploads/bukti_bayar/' . $pembayaran->bukti_bayar))) {
                    unlink(public_path('uploads/bukti_bayar/' . $pembayaran->bukti_bayar));
                }

                // Upload bukti baru
                $uploadPath = public_path('uploads/bukti_bayar');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file = $request->file('bukti_bayar');
                $filename = 'bayar_' . $pembayaran->booking_id . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($uploadPath, $filename);
                $validated['bukti_bayar'] = $filename;
            }

            // Update pembayaran
            $pembayaran->update($validated);

            DB::commit();

            return redirect()->route('admin.transaksi.pembayaran.index')
                ->with('success', 'Pembayaran berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Cleanup uploaded file jika ada error
            if (isset($validated['bukti_bayar']) && file_exists(public_path('uploads/bukti_bayar/' . $validated['bukti_bayar']))) {
                unlink(public_path('uploads/bukti_bayar/' . $validated['bukti_bayar']));
            }

            Log::error('Error updating pembayaran: ' . $e->getMessage());

            return back()
                ->with('error', 'Terjadi kesalahan saat mengupdate pembayaran.')
                ->withInput();
        }
    }

    public function destroy(TransaksiPembayaran $pembayaran)
    {
        $currentUser = Auth::user();

        // Only Admin Cabang can delete
        if ($currentUser->role->kode === 'SUPERADMIN') {
            return back()->with('error', 'Super Admin tidak dapat menghapus pembayaran. Fitur ini khusus untuk Admin Cabang.');
        }

        // Verify pembayaran belongs to admin's cabang
        if ($pembayaran->cabang_id !== $currentUser->cabang_id) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus pembayaran ini!');
        }

        DB::beginTransaction();
        try {
            // Hapus file bukti bayar jika ada
            if ($pembayaran->bukti_bayar && file_exists(public_path('uploads/bukti_bayar/' . $pembayaran->bukti_bayar))) {
                unlink(public_path('uploads/bukti_bayar/' . $pembayaran->bukti_bayar));
            }

            $jenisBayar = $pembayaran->jenis_bayar;
            $pembayaran->delete();

            DB::commit();

            return redirect()->route('admin.transaksi.pembayaran.index')
                ->with('success', "Pembayaran {$jenisBayar} berhasil dihapus!");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting pembayaran: ' . $e->getMessage());

            return redirect()->route('admin.transaksi.pembayaran.index')
                ->with('error', 'Terjadi kesalahan saat menghapus pembayaran.');
        }
    }
}
