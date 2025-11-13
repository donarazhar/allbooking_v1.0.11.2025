<?php

namespace App\Models;

use App\Models\Cabang;
use App\Models\BukaJadwal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JenisAcara extends Model
{
    use HasFactory;

    protected $table = 'jenis_acara';

    protected $fillable = [
        'kode',
        'nama',
        'keterangan',
        'harga',
        'status_jenis_acara',
        'cabang_id',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'status_jenis_acara' => 'string',
    ];

    // Relationships
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function bukaJadwal()
    {
        return $this->hasMany(BukaJadwal::class, 'jenisacara_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status_jenis_acara', 'active');
    }
}