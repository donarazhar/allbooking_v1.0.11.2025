<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Booking;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index()
    {
        $pembayaran = Pembayaran::with(['booking.user', 'booking.bukaJadwal.sesi', 'booking.bukaJadwal.jenisAcara'])
            ->orderBy('tgl_pembayaran', 'desc')
            ->get();
        return view('transaksi.pembayaran.index', compact('pembayaran'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tgl_pembayaran' => 'required|date',
            'booking_id' => 'required|exists:bookings,id',
            'jenis_bayar' => 'required|in:DP,Termin 1,Termin 2,Termin 3,Termin 4,Pelunasan',
            'nominal' => 'required|numeric|min:0',
            'bukti_bayar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'tgl_pembayaran.required' => 'Tanggal pembayaran harus diisi',
            'booking_id.required' => 'Booking harus dipilih',
            'jenis_bayar.required' => 'Jenis bayar harus dipilih',
            'nominal.required' => 'Nominal pembayaran harus diisi',
            'nominal.numeric' => 'Nominal harus berupa angka',
            'nominal.min' => 'Nominal minimal 0',
            'bukti_bayar.image' => 'Bukti bayar harus berupa gambar',
            'bukti_bayar.max' => 'Ukuran bukti bayar maksimal 2MB'
        ]);

        if ($request->hasFile('bukti_bayar')) {
            $file = $request->file('bukti_bayar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/bukti_bayar'), $filename);
            $validated['bukti_bayar'] = $filename;
        }

        Pembayaran::create($validated);

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil ditambahkan!');
    }

    public function update(Request $request, Pembayaran $pembayaran)
    {
        $validated = $request->validate([
            'tgl_pembayaran' => 'required|date',
            'booking_id' => 'required|exists:bookings,id',
            'jenis_bayar' => 'required|in:DP,Termin 1,Termin 2,Termin 3,Termin 4,Pelunasan',
            'nominal' => 'required|numeric|min:0',
            'bukti_bayar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'tgl_pembayaran.required' => 'Tanggal pembayaran harus diisi',
            'booking_id.required' => 'Booking harus dipilih',
            'jenis_bayar.required' => 'Jenis bayar harus dipilih',
            'nominal.required' => 'Nominal pembayaran harus diisi',
            'nominal.numeric' => 'Nominal harus berupa angka',
            'nominal.min' => 'Nominal minimal 0',
            'bukti_bayar.image' => 'Bukti bayar harus berupa gambar',
            'bukti_bayar.max' => 'Ukuran bukti bayar maksimal 2MB'
        ]);

        if ($request->hasFile('bukti_bayar')) {
            if ($pembayaran->bukti_bayar && file_exists(public_path('uploads/bukti_bayar/' . $pembayaran->bukti_bayar))) {
                unlink(public_path('uploads/bukti_bayar/' . $pembayaran->bukti_bayar));
            }
            
            $file = $request->file('bukti_bayar');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/bukti_bayar'), $filename);
            $validated['bukti_bayar'] = $filename;
        }

        $pembayaran->update($validated);

        return redirect()->route('pembayaran.index')
            ->with('success', 'Pembayaran berhasil diupdate!');
    }

    public function destroy(Pembayaran $pembayaran)
    {
        try {
            if ($pembayaran->bukti_bayar && file_exists(public_path('uploads/bukti_bayar/' . $pembayaran->bukti_bayar))) {
                unlink(public_path('uploads/bukti_bayar/' . $pembayaran->bukti_bayar));
            }
            
            $pembayaran->delete();
            return redirect()->route('pembayaran.index')
                ->with('success', 'Pembayaran berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('pembayaran.index')
                ->with('error', 'Pembayaran tidak dapat dihapus!');
        }
    }
}
