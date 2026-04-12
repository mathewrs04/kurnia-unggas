<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nama_produk',
        'tipe_produk',
        'satuan',
        'harga_satuan',
        'user_id',
    ];

    /**
     * Get formatted tipe produk for display
     */
    public function getTipeProdukFormattedAttribute()
    {
        $labels = [
            'ayam_hidup' => 'Ayam Hidup',
            'jasa' => 'Jasa',
            'barang_operasional' => 'Barang Operasional',
            'biaya_operasional' => 'Biaya Operasional',
        ];

        return $labels[$this->tipe_produk] ?? ucwords(str_replace('_', ' ', $this->tipe_produk));
    }
}
