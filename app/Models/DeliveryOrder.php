<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryOrder extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'kode_do',
        'peternak_id',
        'total_jumlah_ekor',
        'total_berat',
        'tanggal_do',
    ];

    protected $casts = [
        'tanggal_do' => 'date',
        'total_jumlah_ekor' => 'integer',
        'total_berat' => 'float',
    ];

    // Relasi ke Peternak
    public function peternak()
    {
        return $this->belongsTo(Peternak::class);
    }

    // Relasi ke Pembelian Detail (one to one)
    public function pembelianDetail()
    {
        return $this->hasOne(PembelianDetail::class);
    }

    //generate kode DO otomatis
    public static function kodeDO()
    {
        $prefix = 'DO-';
        $maxId = self::withTrashed()->max('id');
        $kodeDO = $prefix . str_pad($maxId + 1, 5, '0', STR_PAD_LEFT);
        return $kodeDO;
    }

}
