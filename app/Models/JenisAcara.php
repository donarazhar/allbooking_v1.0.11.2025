<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisAcara extends Model
{
    protected $table = 'jenis_acara';

    protected $fillable = [
        'kode',
        'nama',
        'keterangan',
        'harga',
        'status_jenis_acara'
    ];

    public function bukaJadwal()
    {
        return $this->hasMany(BukaJadwal::class, 'jenisacara_id');
    }
}
