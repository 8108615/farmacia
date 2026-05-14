<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presentacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
    ];

    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = mb_strtoupper(trim((string) $value));
    }

    public function getNombreAttribute($value)
    {
        return mb_strtoupper((string) $value);
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}
