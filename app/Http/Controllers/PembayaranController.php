<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembayaranController extends Controller
{
    public function index()
    {
        $pembayaran = TransaksiPembayaran::with([
            'bookings.user',
            'bookings.bukaJadwal.sesi',
            'bookings.bukaJadwal.jenisAcara'
        ])
            ->orderBy('tgl_pembayaran', 'desc')
            ->get();
        // dd($pembayaran);
        return view('transaksi.pembayaran.index', compact('pembayaran'));
    }

    public function store(Request $request)
    {
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

        DB::beginTransaction();
        try {
            // Upload bukti bayar jika ada
            if ($request->hasFile('bukti_bayar')) {
                $file = $request->file('bukti_bayar');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/bukti_bayar'), $filename);
                $validated['bukti_bayar'] = $filename;
            }

            // Create pembayaran
            Pembayaran::create($validated);

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

    public function update(Request $request, Pembayaran $pembayaran)
    {
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
                $file = $request->file('bukti_bayar');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/bukti_bayar'), $filename);
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

    public function destroy(Pembayaran $pembayaran)
    {
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
