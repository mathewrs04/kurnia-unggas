<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BiayaOperasional extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'no_nota',
        'produk_id',
        'metode_pembayaran_id',
        'tanggal_biaya',
        'foto_nota',
        'harga_satuan',
        'jumlah',
        'subtotal',
        'user_id',
    ];

    protected $casts = [
        'tanggal_biaya' => 'date',
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function metodePembayaran()
    {
        return $this->belongsTo(MetodePembayaran::class);
    }

    public static function generateNoNota(): string
    {
        $prefix = 'BO-';
        $date = date('Ymd');
        $lastNota = self::whereDate('created_at', today())
            ->orderByDesc('id')
            ->first();

        $nextNumber = $lastNota
            ? intval(substr($lastNota->no_nota, -4)) + 1
            : 1;

        return $prefix . $date . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
