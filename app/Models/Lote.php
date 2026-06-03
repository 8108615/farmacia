<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Producto;
use App\Models\Proveedor;

class Lote extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id',
        'proveedor_id',
        'numero_lote',
        'fecha_vencimiento',
        'fecha_fabricacion',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_fabricacion' => 'date',
    ];

    public function getNombreAttribute()
    {
        return $this->attributes['numero_lote'] ?? null;
    }

    public function setNombreAttribute($value)
    {
        $this->attributes['numero_lote'] = $value;
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function compraDetalles()
    {
        return $this->hasMany(CompraDetalle::class, 'lote_id');
    }
}
