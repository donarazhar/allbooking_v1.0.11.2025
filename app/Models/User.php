<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
        'cabang_id',
        'status_users',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'tgl_lahir' => 'date',
        'jenis_kelamin' => 'string',
        'status_users' => 'string',
    ];

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function transaksiBooking()
    {
        return $this->hasMany(TransaksiBooking::class, 'user_id');
    }

    // Helper Methods
    public function isSuperAdmin()
    {
        return $this->role->kode === 'SUPERADMIN';
    }

    public function isAdmin()
    {
        return $this->role->kode === 'ADMIN';
    }

    public function isPimpinan()
    {
        return $this->role->kode === 'PIMPINAN';
    }
}
