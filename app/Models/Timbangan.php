<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timbangan extends Model
{
    protected $fillable = [
        'jenis',
        'tanggal',
        'total_jumlah_ekor',
        'total_berat',
        'nama_karyawan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_jumlah_ekor' => 'integer',
        'total_berat' => 'float',
    ];

    // Relasi ke Keranjang (one to many)
    public function keranjangs()
    {
        return $this->hasMany(Keranjang::class);
    }

    // Relasi ke Pembelian Detail (one to many)
    public function pembelianDetails()
    {
        return $this->hasMany(PembelianDetail::class);
    }
}
