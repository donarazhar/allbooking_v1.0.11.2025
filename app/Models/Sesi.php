<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sesi extends Model
{
    protected $table = 'sesi';

    protected $fillable = [
        'kode',
        'nama',
        'jam_mulai',
        'jam_selesai',
        'keterangan'
    ];

    public function bukaJadwal()
    {
        return $this->hasMany(BukaJadwal::class);
    }
}
