<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pelanggan extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'nama',
        'alamat',
        'no_telp',
        'user_id',
    ];

}
