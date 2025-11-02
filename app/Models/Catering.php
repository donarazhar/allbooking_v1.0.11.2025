<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catering extends Model
{
    protected $table = 'catering';

    protected $fillable = [
        'nama',
        'email',
        'no_hp',
        'alamat',
        'foto',
        'keterangan'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
