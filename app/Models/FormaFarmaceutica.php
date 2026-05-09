<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormaFarmaceutica extends Model
{
    Use HasFactory;
    protected $fillable = [
        'nombre',
    ];

    
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
