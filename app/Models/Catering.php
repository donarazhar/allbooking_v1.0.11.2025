<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catering extends Model
{
    /**
     * Mendefinisikan tabel database yang digunakan oleh model ini.
     */
    protected $table = 'catering';

    /**
     * Mendefinisikan kolom mana saja yang boleh diisi saat membuat atau mengubah data.
     */
    protected $fillable = [
        'nama',
        'email',
        'no_hp',
        'alamat',
        'password',
        'foto',
        'keterangan'
    ];

    /**
     * Mendefinisikan relasi ke model Booking.
     * Satu catering dapat memiliki banyak booking.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
