<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk mengelola data pada tabel `jenis_acara`.
 */
class JenisAcara extends Model
{
    /**
     * Mendefinisikan tabel database yang digunakan oleh model ini.
     */
    protected $table = 'jenis_acara';

    /**
     * Mendefinisikan kolom mana saja yang boleh diisi saat membuat atau mengubah data.
     */
    protected $fillable = [
        'kode',
        'nama',
        'keterangan',
        'harga',
        'status_jenis_acara'
    ];

    /**
     * Mendefinisikan relasi ke model BukaJadwal.
     * Satu JenisAcara dapat memiliki banyak BukaJadwal.
     */
    public function bukaJadwal()
    {
        return $this->hasMany(BukaJadwal::class, 'jenisacara_id');
    }
}
