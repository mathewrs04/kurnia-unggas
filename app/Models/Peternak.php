<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peternak extends Model
{
    protected $fillable = [
        'pemasok_id',
        'nama',
        'alamat',
        'no_telp'
    ];

    // Relasi ke Pemasok
    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class);
    }

    // Relasi ke Pembelian (one to many)
    public function pembelians()
    {
        return $this->hasMany(Pembelian::class);
    }
}
