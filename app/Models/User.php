<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model untuk data pengguna (user) di tabel `users`.
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * Kolom yang boleh diisi massal.
     */
    protected $fillable = [
        'nama',
        'email',
        'password',
        'no_hp',
        'alamat',
        'role_id',
        'foto',
        'status_users'
    ];

    /**
     * Kolom yang disembunyikan (cth: password).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relasi ke Role (satu user punya satu role).
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relasi ke Booking (satu user punya banyak booking).
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
