<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MortalitasAyam extends Model
{
    protected $fillable = [
        'user_id',
        'batch_pembelian_id',
        'tanggal_mati',
        'berat_kg',
        'jumlah_ekor',
        'catatan',
    ];

    protected $casts = [
        'tanggal_mati' => 'date',
        'berat_kg' => 'float',
        'jumlah_ekor' => 'integer',
    ];

    public function batch()
    {
        return $this->belongsTo(BatchPembelian::class, 'batch_pembelian_id');
    }
}
