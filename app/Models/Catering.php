<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catering extends Model
{
    use HasFactory;

    protected $table = 'catering';

    protected $fillable = [
        'nama',
        'email',
        'no_hp',
        'alamat',
        'password',
        'keterangan',
        'foto',
    ];

    protected $hidden = [
        'password',
    ];

    // Many to Many dengan Cabang
    public function cabang()
    {
        return $this->belongsToMany(Cabang::class, 'cabang_catering', 'catering_id', 'cabang_id')
            ->withTimestamps();
    }

    // Relationship dengan TransaksiBooking
    public function transaksiBooking()
    {
        return $this->hasMany(TransaksiBooking::class, 'catering_id');
    }
}
