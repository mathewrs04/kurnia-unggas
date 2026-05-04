<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Penjualan;

class Pelanggan extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'nama',
        'alamat',
        'no_telp',
        'user_id',
    ];

    public function penjualans()
    {
        return $this->hasMany(Penjualan::class);
    }

}
