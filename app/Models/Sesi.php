<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sesi extends Model
{
    /**
     * Mendefinisikan tabel database yang digunakan oleh model ini.
     */
    protected $table = 'sesi';

    /**
     * Mendefinisikan kolom mana saja yang boleh diisi saat membuat atau mengubah data.
     */
    protected $fillable = [
        'kode',
        'nama',
        'jam_mulai',
        'jam_selesai',
        'keterangan'
    ];

    /**
     * Mendefinisikan relasi ke model BukaJadwal.
     * Satu Sesi dapat memiliki banyak BukaJadwal.
     */
    public function bukaJadwal()
    {
        return $this->hasMany(BukaJadwal::class);
    }
}
