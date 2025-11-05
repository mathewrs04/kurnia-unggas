<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    protected $fillable = [
        'kode_do',
        'tanggal',
        'jumlah_ekor',
        'berat_total',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_ekor' => 'integer',
        'berat_total' => 'float',
    ];

    // Relasi ke Pembelian Detail (one to many)
    public function pembelianDetails()
    {
        return $this->hasMany(PembelianDetail::class);
    }

    //generate kode DO otomatis
    public static function kodeDO()
    {
        $prefix = 'DO-';
        $maxId = self::max('id');
        $kodeDO = $prefix . str_pad($maxId + 1, 5, '0', STR_PAD_LEFT);
        return $kodeDO;
    }

}
