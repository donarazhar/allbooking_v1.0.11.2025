<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'buka_jadwal_id',
        'tanggal_booking',
        'catering_id',
        'status_bookings',
        'keterangan',
        'tgl_expired_booking'
    ];

    protected $casts = [
        'tanggal_booking' => 'date',
        'tgl_expired_booking' => 'date'
    ];

    // Accessor untuk cek apakah booking sudah expired
    public function getIsExpiredAttribute()
    {
        if (!$this->tgl_expired_booking) return false;
        return Carbon::now()->isAfter($this->tgl_expired_booking);
    }

    // Accessor untuk cek apakah booking mendekati expired (3 hari sebelum)
    public function getIsNearExpiryAttribute()
    {
        if (!$this->tgl_expired_booking) return false;
        $daysUntilExpiry = Carbon::now()->diffInDays($this->tgl_expired_booking, false);
        return $daysUntilExpiry >= 0 && $daysUntilExpiry <= 3;
    }

    // Accessor untuk mendapatkan sisa hari
    public function getDaysUntilExpiryAttribute()
    {
        if (!$this->tgl_expired_booking) return null;
        return Carbon::now()->diffInDays($this->tgl_expired_booking, false);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bukaJadwal()
    {
        return $this->belongsTo(BukaJadwal::class);
    }

    public function catering()
    {
        return $this->belongsTo(Catering::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }
}
