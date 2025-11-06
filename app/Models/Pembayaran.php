<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'transaksi_pembayaran';

    protected $fillable = [
        'tgl_pembayaran',
        'booking_id',
        'jenis_bayar',
        'bukti_bayar',
        'nominal'
    ];

    public function bookings()
    {
        return $this->belongsTo(Booking::class,'booking_id');
    }
}
