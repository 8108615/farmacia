<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraDetalle extends Model
{
    protected $table = 'compra_detalles';

    protected $fillable = [
        'compra_id',
        'producto_id',
        'lote_id',
        'precio_compra_unidad',
        'precio_venta_unidad',
        'porcentaje_ganancia_unidad',
        'cantidad',
    ];

    protected $casts = [
        'precio_compra_unidad' => 'decimal:2',
        'precio_venta_unidad' => 'decimal:2',
        'porcentaje_ganancia_unidad' => 'decimal:2',
        'cantidad' => 'integer',
    ];

    // Relaciones
    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }
}
