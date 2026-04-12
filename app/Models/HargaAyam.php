<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HargaAyam extends Model
{
    protected $fillable = [
        'user_id',
        'produks_id',
        'tanggal',
        'harga_eceran',
        'harga_partai',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produks_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
