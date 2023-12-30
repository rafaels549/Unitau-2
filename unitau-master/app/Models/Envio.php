<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Envio extends Model
{
    use HasFactory;

    protected $fillable = [
      
         'user_id',
    ];


   
    public function files() {
        return $this->hasMany(EnvioFiles::class);
    }

   
}
