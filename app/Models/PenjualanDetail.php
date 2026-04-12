<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    protected $fillable = [
        'penjualan_id',
        'produk_id',
        'batch_id',
        'timbangan_id',
        'deskripsi',
        'jumlah_ekor',
        'jumlah_berat',
        'harga_satuan',
        'subtotal'
    ];

    // Relationships
    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function batch()
    {
        return $this->belongsTo(BatchPembelian::class, 'batch_id');
    }

    public function timbangan()
    {
        return $this->belongsTo(Timbangan::class);
    }
}
