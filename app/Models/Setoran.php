<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setoran extends Model
{
    use SoftDeletes;

    const STATUS_MENUNGGU_ACC = 'menunggu_acc';
    const STATUS_DISETUJUI = 'disetujui';

    protected $fillable = [
        'kode_setoran',
        'tanggal_setoran',
        'nominal',
        'status',
        'keterangan',
        'kasir_id',
        'acc_by',
        'acc_at',
    ];

    protected $casts = [
        'tanggal_setoran' => 'date',
        'acc_at' => 'datetime',
    ];

    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'acc_by');
    }

    public static function generateKodeSetoran(): string
    {
        $prefix = 'STR-';
        $date = date('Ymd');
        $lastSetoran = self::whereDate('created_at', today())
            ->orderByDesc('id')
            ->first();

        $nextNumber = $lastSetoran
            ? intval(substr($lastSetoran->kode_setoran, -4)) + 1
            : 1;

        return $prefix . $date . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeTanggalRange($query, $dariTanggal, $sampaiTanggal)
    {
        if ($dariTanggal && $sampaiTanggal) {
            return $query->whereBetween('tanggal_setoran', [$dariTanggal, $sampaiTanggal]);
        }

        return $query;
    }
}
