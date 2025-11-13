<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiPembayaran extends Model
{
    use HasFactory;

    protected $table = 'transaksi_pembayaran';

    protected $fillable = [
        'tgl_pembayaran',
        'booking_id',
        'jenis_bayar',
        'bukti_bayar',
        'nominal',
        'cabang_id',
    ];

    protected $casts = [
        'tgl_pembayaran' => 'date',
        'nominal' => 'decimal:2',
        'jenis_bayar' => 'string',
    ];

    // Relationships
    public function transaksiBooking()
    {
        return $this->belongsTo(TransaksiBooking::class, 'booking_id');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    // Scopes
    public function scopeByJenisBayar($query, $jenis)
    {
        return $query->where('jenis_bayar', $jenis);
    }
}
