<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $fillable = [
        'nama',
        'posisi',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi many-to-many ke Timbangan melalui pivot table karyawan_timbangan
    public function timbangans()
    {
        return $this->belongsToMany(Timbangan::class, 'karyawan_timbangan');
    }
}
