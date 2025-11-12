<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // ✅ MAKE SURE THIS IS CORRECT
    protected $table = 'users'; // Should be 'users' not 'transaksi_users' or anything else

    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_hp',
        'alamat',
        'jenis_kelamin',
        'tgl_lahir',
        'nik',
        'provinsi_id',
        'provinsi_nama',
        'kabupaten_id',
        'kabupaten_nama',
        'kecamatan_id',
        'kecamatan_nama',
        'kelurahan_id',
        'kelurahan_nama',
        'kode_pos',
        'foto',
        'role_id',
        'status_users',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'tgl_lahir' => 'date',
    ];

    // ✅ REMOVE THIS IF EXISTS (causes usersupdated_at issue)
    // const UPDATED_AT = 'usersupdated_at'; // ← DELETE THIS LINE!
    // const CREATED_AT = 'userscreated_at'; // ← DELETE THIS LINE!

    // Relations
    public function role()
    {
        return $this->belongsTo(Role::class,'role_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}