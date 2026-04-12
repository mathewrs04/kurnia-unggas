<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penjualan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'no_nota',
        'tanggal_jual',
        'tipe_penjualan',
        'diskon',
        'subtotal',
        'pelanggan_id',
        'user_id'
    ];

    protected $casts = [
        'tanggal_jual' => 'date',
    ];

    // Relationship
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function penjualanDetails()
    {
        return $this->hasMany(PenjualanDetail::class);
    }

    // Helper method untuk generate nomor nota
    public static function generateNoNota()
    {
        $prefix = 'NJ-';
        $date = date('Ymd');
        $lastNota = self::whereDate('created_at', date('Y-m-d'))
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastNota) {
            $lastNumber = intval(substr($lastNota->no_nota, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $date . '-' . $newNumber;
    }
}
