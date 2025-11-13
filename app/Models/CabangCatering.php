<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CabangCatering extends Pivot
{
    use HasFactory;

    protected $table = 'cabang_catering';

    protected $fillable = [
        'cabang_id',
        'catering_id',
    ];

    // Relationships
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    }

    public function catering()
    {
        return $this->belongsTo(Catering::class, 'catering_id');
    }
}