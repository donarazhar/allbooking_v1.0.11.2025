<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukaJadwal extends Model
{
    use HasFactory;

    protected $table = 'buka_jadwal';

    protected $fillable = [
        'cabang_id',
        'hari',
        'tanggal',
        'sesi_id',
        'jenisacara_id',
        'status_jadwal',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'status_jadwal' => 'string',
    ];

    // Relationships
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function sesi()
    {
        return $this->belongsTo(Sesi::class, 'sesi_id');
    }

    public function jenisAcara()
    {
        return $this->belongsTo(JenisAcara::class, 'jenisacara_id');
    }

    public function transaksiBooking()
    {
        return $this->hasMany(TransaksiBooking::class, 'bukajadwal_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status_jadwal', 'available');
    }

    public function scopeBooked($query)
    {
        return $query->where('status_jadwal', 'booked');
    }

    // Helper Methods
    public function isAvailable()
    {
        return $this->status_jadwal === 'available';
    }

    public function markAsBooked()
    {
        $this->update(['status_jadwal' => 'booked']);
    }

    public function markAsAvailable()
    {
        $this->update(['status_jadwal' => 'available']);
    }
}
