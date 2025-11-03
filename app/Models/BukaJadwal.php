<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model BukaJadwal merepresentasikan jadwal yang tersedia untuk dibooking.
class BukaJadwal extends Model
{
    // Menentukan nama tabel database yang terhubung dengan model ini.
    protected $table = 'buka_jadwal';

    // Mendefinisikan kolom-kolom yang boleh diisi secara massal (mass assignable).
    protected $fillable = [
        'hari',
        'tanggal',
        'sesi_id',
        'jenisacara_id'
    ];

    // Setiap jadwal yang dibuka pasti memiliki satu sesi.
    public function sesi()
    {
        return $this->belongsTo(Sesi::class);
    }

    // Setiap jadwal yang dibuka terkait dengan satu jenis acara.
    public function jenisAcara()
    {
        return $this->belongsTo(JenisAcara::class, 'jenisacara_id');
    }

    // Satu jadwal yang dibuka bisa memiliki banyak booking (jika sistem mengizinkannya).
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
