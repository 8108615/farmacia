<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $fillable = [
        'sucursal_id',
        'nombre',
        'estado',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function arqueos()
    {
        return $this->hasMany(Arqueo::class);
    }
}
