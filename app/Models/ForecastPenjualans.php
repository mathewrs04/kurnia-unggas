<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastPenjualans extends Model
{
    protected $fillable = [
        'tanggal',
        'prediksi',
        'lower',
        'upper'
    ];
}
