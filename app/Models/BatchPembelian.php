<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BatchPembelian extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'kode_batch',
        'harga_beli_per_kg',
        'stok_ekor',
        'stok_ekor_minimal',
        'stok_kg',
        'user_id',
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

    public function stokOpnames()
    {
        return $this->hasMany(StokOpname::class, 'batch_pembelian_id');
    }

    public function mortalitas()
    {
        return $this->hasMany(MortalitasAyam::class, 'batch_pembelian_id');
    }

    // Relasi ke Penjualan Detail (one to many)
    public function penjualanDetails()
    {
        return $this->hasMany(PenjualanDetail::class, 'batch_id');
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
