<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timbangan extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'jenis',
        'tanggal',
        'total_jumlah_ekor',
        'total_berat',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_jumlah_ekor' => 'integer',
        'total_berat' => 'float',
    ];

    // Relasi many-to-many ke Karyawan melalui pivot table karyawan_timbangan
    public function karyawans()
    {
        return $this->belongsToMany(Karyawan::class, 'karyawan_timbangan');
    }

    // Relasi ke Keranjang (one to many)
    public function keranjangs()
    {
        return $this->hasMany(Keranjang::class);
    }

    // Relasi ke Delivery Order (one to many)
    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class);
    }

    // Relasi ke Pembelian Detail (one to many)
    public function pembelianDetails()
    {
        return $this->hasMany(PembelianDetail::class);
    }
}
