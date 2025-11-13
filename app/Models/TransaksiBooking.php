<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiBooking extends Model
{
    use HasFactory;

    protected $table = 'transaksi_booking';

    protected $fillable = [
        'cabang_id',
        'user_id',
        'bukajadwal_id',
        'catering_id',
        'tgl_booking',
        'tgl_expired_booking',
        'status_booking',
        'keterangan',
    ];

    protected $casts = [
        'tgl_booking' => 'date',
        'tgl_expired_booking' => 'datetime',
        'status_booking' => 'string',
    ];

    // Relationships
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bukaJadwal()
    {
        return $this->belongsTo(BukaJadwal::class, 'bukajadwal_id');
    }

    public function catering()
    {
        return $this->belongsTo(Catering::class, 'catering_id');
    }

    public function transaksiPembayaran()
    {
        return $this->hasMany(TransaksiPembayaran::class, 'booking_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status_booking', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status_booking', 'expired');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status_booking', 'cancelled');
    }

    // Helper Methods
    public function isActive()
    {
        return $this->status_booking === 'active';
    }

    public function isExpired()
    {
        return $this->status_booking === 'expired';
    }

    public function getTotalPembayaran()
    {
        return $this->transaksiPembayaran()->sum('nominal');
    }
}
