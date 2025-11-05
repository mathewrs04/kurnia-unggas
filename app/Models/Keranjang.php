<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    protected $fillable = [
        'timbangan_id',
        'jumlah_ekor',
        'berat_ayam',
    ];

    protected $casts = [
        'jumlah_ekor' => 'integer',
        'berat_ayam' => 'float',
    ];

    // Relasi ke Timbangan
    public function timbangan()
    {
        return $this->belongsTo(Timbangan::class);
    }
}
