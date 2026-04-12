<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pemasok extends Model
{
   use SoftDeletes;
   
   protected $guarded = [ 'id' ];
  
   public function peternaks()
   {
       return $this->hasMany(Peternak::class);
   }
}
