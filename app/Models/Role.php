<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Role extends Model
{
    /**
     * Mendefinisikan kolom mana saja yang boleh diisi secara massal (mass assignable).
     * Ini adalah mekanisme keamanan untuk mencegah pengisian kolom yang tidak diinginkan.
     *
     * @var array
     */
    protected $fillable = [
        'kode',
        'nama',
        'keterangan'
    ];

    /**
     * Mendefinisikan relasi "one-to-many" ke model User.
     * Artinya, satu peran (Role) dapat dimiliki oleh banyak pengguna (User).
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
