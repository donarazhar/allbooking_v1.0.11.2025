<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukaJadwal extends Model
{
    use HasFactory;

    protected $table = 'buka_jadwal';

    protected $fillable = [
        'hari',
        'tanggal',
        'sesi_id',
        'jenisacara_id',
        'status_jadwal'
    ];

    protected $casts = [
        'tanggal' => 'date'
    ];

    /**
     * Relasi ke Sesi
     */
    public function sesi()
    {
        return $this->belongsTo(Sesi::class, 'sesi_id');
    }

    /**
     * Relasi ke Jenis Acara
     */
    public function jenisAcara()
    {
        return $this->belongsTo(JenisAcara::class, 'jenisacara_id');
    }

    /**
     * Relasi ke Transaksi Booking
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'bukajadwal_id');
    }

    /**
     * LOGIC: Auto-determine status berdasarkan booking aktif
     * 
     * Jika ada booking dengan status_booking = 'active', maka jadwal = 'booked'
     * Jika tidak ada booking aktif, maka jadwal = 'available'
     */
    public function hasActiveBooking()
    {
        return $this->bookings()
            ->where('status_booking', 'active')
            ->exists();
    }

    /**
     * Get computed status berdasarkan booking
     */
    public function getComputedStatusAttribute()
    {
        return $this->hasActiveBooking() ? 'booked' : 'available';
    }

    /**
     * Sync status jadwal berdasarkan booking aktif
     * Method ini dipanggil setelah create/update/delete booking
     */
    public function syncStatus()
    {
        $newStatus = $this->hasActiveBooking() ? 'booked' : 'available';
        
        // Only update if status changed
        if ($this->status_jadwal !== $newStatus) {
            $this->update(['status_jadwal' => $newStatus]);
        }
        
        return $newStatus;
    }

    /**
     * Scope: Only available jadwal
     */
    public function scopeAvailable($query)
    {
        return $query->where('status_jadwal', 'available');
    }

    /**
     * Scope: Only booked jadwal
     */
    public function scopeBooked($query)
    {
        return $query->where('status_jadwal', 'booked');
    }

    /**
     * Check apakah jadwal bisa dibook
     */
    public function isBookable()
    {
        return !$this->hasActiveBooking();
    }
}