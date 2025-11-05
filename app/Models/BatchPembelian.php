<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchPembelian extends Model
{
    protected $fillable = [
        'kode_batch',
        'harga_beli_per_kg',
        'stok_ekor',
        'stok_ekor_minimal',
        'stok_kg',
    ];

    protected $casts = [
        'harga_beli_per_kg' => 'integer',
        'stok_ekor' => 'integer',
        'stok_ekor_minimal' => 'integer',
        'stok_kg' => 'float',
    ];

    // Relasi ke Pembelian Detail (one to many)
    public function pembelianDetails()
    {
        return $this->hasMany(PembelianDetail::class);
    }

    // Generate kode batch otomatis
    public static function kodeBatch()
    {
        $prefix = 'BATCH-';
        $maxId = self::max('id');
        $kodeBatch = $prefix . str_pad($maxId + 1, 5, '0', STR_PAD_LEFT);
        return $kodeBatch;
    }
}
