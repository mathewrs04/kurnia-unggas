<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PembelianDetail extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'pembelian_id',
        'batch_pembelian_id',
        'produk_id',
        'timbangan_id',
        'delivery_order_id',
        'harga_beli_per_kg',
        'subtotal',
        'susut_kg',
    ];

    protected $casts = [
        'harga_beli_per_kg' => 'integer',
        'subtotal' => 'integer',
        'susut_kg' => 'float',
    ];

    // Relasi ke Pembelian
    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    // Relasi ke Batch Pembelian
    public function batchPembelian()
    {
        return $this->belongsTo(BatchPembelian::class);
    }

    // Relasi ke Timbangan
    public function timbangan()
    {
        return $this->belongsTo(Timbangan::class);
    }

    // Relasi ke Delivery Order
    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class);
    }

    // Relasi ke Metode Pembayaran
    public function metodePembayaran()
    {
        return $this->belongsTo(MetodePembayaran::class);
    }
}
