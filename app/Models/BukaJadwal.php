<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BukaJadwal extends Model
{
    protected $table = 'buka_jadwal';

    protected $fillable = [
        'hari',
        'tanggal',
        'sesi_id',
        'jenisacara_id'
    ];

    public function sesi()
    {
        return $this->belongsTo(Sesi::class);
    }

    public function jenisAcara()
    {
        return $this->belongsTo(JenisAcara::class, 'jenisacara_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
