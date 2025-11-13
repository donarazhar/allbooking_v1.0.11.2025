<?php

namespace App\Models;

use App\Models\Cabang;
use App\Models\BukaJadwal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sesi extends Model
{
    use HasFactory;

    protected $table = 'sesi';

    protected $fillable = [
        'kode',
        'nama',
        'jam_mulai',
        'jam_selesai',
        'keterangan',
        'cabang_id',
    ];

    protected $casts = [
        'jam_mulai' => 'datetime:H:i',
        'jam_selesai' => 'datetime:H:i',
    ];

    // Relationships
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function bukaJadwal()
    {
        return $this->hasMany(BukaJadwal::class, 'sesi_id');
    }
}