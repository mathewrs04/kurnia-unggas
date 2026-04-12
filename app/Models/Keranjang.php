<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Keranjang extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'timbangan_id',
        'jumlah_ekor',
        'berat_keranjang',
        'berat_total',
        'berat_ayam',
    ];

    protected $casts = [
        'jumlah_ekor' => 'integer',
        'berat_keranjang' => 'float',
        'berat_total' => 'float',
        'berat_ayam' => 'float',
    ];

    // Relasi ke Timbangan
    public function timbangan()
    {
        return $this->belongsTo(Timbangan::class);
    }
}
