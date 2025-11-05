<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $fillable = [
        'tanggal_pembelian',
        'kode_pembelian',
        'status',
        'peternak_id',
    ];

    protected $casts = [
        'tanggal_pembelian' => 'date',
    ];

    // Relasi ke Peternak
    public function peternak()
    {
        return $this->belongsTo(Peternak::class);
    }

    // Relasi ke Pembelian Detail (one to many)
    public function pembelianDetails()
    {
        return $this->hasMany(PembelianDetail::class);
    }

    // Generate kode pembelian otomatis
    public static function generateKodePembelian()
    {
        $prefix = 'PBL-';
        $date = date('Ymd');
        $lastPembelian = self::whereDate('created_at', today())
            ->latest('id')
            ->first();
        
        if ($lastPembelian) {
            $lastNumber = intval(substr($lastPembelian->kode_pembelian, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . $date . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // Scope untuk filter berdasarkan status
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal_pembelian', $tanggal);
    }
}
