<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokOpname extends Model
{
    protected $fillable = [
        'user_id',
        'batch_pembelian_id',
        'timbangan_id',
        'tanggal_opname',
        'stok_ekor_sistem',
        'stok_kg_sistem',
        'berat_aktual_kg',
        'susut_kg',
        'catatan',
    ];

    protected $casts = [
        'tanggal_opname' => 'date',
        'stok_ekor_sistem' => 'integer',
        'stok_kg_sistem' => 'float',
        'berat_aktual_kg' => 'float',
        'susut_kg' => 'float',
    ];

    public function batch()
    {
        return $this->belongsTo(BatchPembelian::class, 'batch_pembelian_id');
    }

    public function timbangan()
    {
        return $this->belongsTo(Timbangan::class);
    }
}
