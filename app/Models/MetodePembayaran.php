<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MetodePembayaran extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama_metode',
        'keterangan',
    ];

    public function pembelianDetails()
    {
        return $this->hasMany(PembelianDetail::class, 'metode_pembayaran', 'nama_metode');
    }

    public function penjualanDetails()
    {
        return $this->hasMany(PenjualanDetail::class, 'metode_pembayaran', 'nama_metode');
    }
    
    public function biayaOperasionals()
    {
        return $this->hasMany(BiayaOperasional::class);
    }
}
