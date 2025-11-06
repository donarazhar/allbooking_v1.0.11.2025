<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

// Model untuk data Booking.
class Booking extends Model
{
    protected $table = 'transaksi_booking';
    // Kolom yang bisa diisi.
    protected $fillable = [
        'user_id',
        'bukajadwal_id',
        'tgl_booking',
        'catering_id',
        'status_booking',
        'keterangan',
        'tgl_expired_booking'
    ];

    // Casting tipe data kolom.
    protected $casts = [
        'tgl_booking' => 'date',
        'tgl_expired_booking' => 'date'
    ];

    // Accessor: Cek jika booking sudah expired.
    public function getIsExpiredAttribute()
    {
        if (!$this->tgl_expired_booking) return false;
        return Carbon::now()->isAfter($this->tgl_expired_booking);
    }

    // Accessor: Cek jika booking mendekati expired (<= 3 hari).
    public function getIsNearExpiryAttribute()
    {
        if (!$this->tgl_expired_booking) return false;
        $daysUntilExpiry = Carbon::now()->diffInDays($this->tgl_expired_booking, false);
        return $daysUntilExpiry >= 0 && $daysUntilExpiry <= 3;
    }

    // Accessor: Hitung sisa hari sampai expired.
    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->tgl_expired_booking) return null;
        return Carbon::now()->diffInDays($this->tgl_expired_booking, false);
    }

    // Relasi: Booking ini milik satu User.
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi: Booking ini untuk satu BukaJadwal.
    public function bukaJadwal()
    {
        return $this->belongsTo(BukaJadwal::class,'bukajadwal_id');
    }

    // Relasi: Booking ini bisa memiliki satu Catering.
    public function catering()
    {
        return $this->belongsTo(Catering::class);
    }

    // Relasi: Booking ini bisa punya banyak Pembayaran.
    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }
}
