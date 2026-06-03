<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Compra;

class Inventario extends Model
{
    protected $table = 'inventarios';

    protected $fillable = [
        'compra_id',
        'lote_id',
        'ubicacion_fisica_id',
        'sucursal_id',
        'producto_id',
        'precio_compra_unidad',
        'precio_venta_unidad',
        'stock_actual',
        'stock_minimo',
        'stock_maximo',
        'fecha_registro',
        'estado',
    ];

    protected $casts = [
        'precio_compra_unidad' => 'decimal:2',
        'precio_venta_unidad' => 'decimal:2',
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
        'stock_maximo' => 'integer',
        'fecha_registro' => 'date',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'compra_id');
    }
}
