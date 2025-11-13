<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;

    protected $table = 'cabang';

    protected $fillable = [
        'kode',
        'nama',
        'alamat',
        'kota',
        'no_telp',
    ];

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class, 'cabang_id');
    }

    public function sesi()
    {
        return $this->hasMany(Sesi::class, 'cabang_id');
    }

    public function jenisAcara()
    {
        return $this->hasMany(JenisAcara::class, 'cabang_id');
    }

    public function bukaJadwal()
    {
        return $this->hasMany(BukaJadwal::class, 'cabang_id');
    }

    public function transaksiBooking()
    {
        return $this->hasMany(TransaksiBooking::class, 'cabang_id');
    }

    public function transaksiPembayaran()
    {
        return $this->hasMany(TransaksiPembayaran::class, 'cabang_id');
    }

    // Many to Many dengan Catering
    public function catering()
    {
        return $this->belongsToMany(Catering::class, 'cabang_catering', 'cabang_id', 'catering_id')
            ->withTimestamps();
    }
}
