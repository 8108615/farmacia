<?php

namespace App\Models;

use App\Models\Sucursal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UbicacionFisica extends Model
{
    use HasFactory;

    protected $fillable = [
        'sucursal_id',
        'nombre',
        'descripcion',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }
}
