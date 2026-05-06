<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peternak extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'pemasok_id',
        'nama',
        'alamat',
        'no_telp',
        'user_id'
    ];

    // Relasi ke Pemasok
    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class);
    }

    // Relasi ke Delivery Order (one to many)
    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    // Relasi ke Pembelian (one to many)
    public function pembelians()
    {
        return $this->hasMany(Pembelian::class);
    }

    // Relasi ke Pembelian Detail (through Pembelian)
    public function pembelianDetails()
    {
        return $this->hasManyThrough(
            PembelianDetail::class,
            Pembelian::class,
            'peternak_id',
            'pembelian_id',
            'id',
            'id'
        );
    }
}
