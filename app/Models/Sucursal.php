<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sucursal extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function compraTmps()
    {
        return $this->hasMany(CompraTmp::class, 'sucursal_id');
    }

    public function compras()
    {
        return $this->hasMany(Compra::class, 'sucursal_id');
    }
}
