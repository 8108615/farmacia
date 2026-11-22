<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    protected $table = 'movimientos_inventario';

    public $timestamps = false;

    protected $fillable = [
        'inventario_id',
        'producto_id',
        'lote_id',
        'sucursal_id',
        'usuario_id',
        'codigo_producto',
        'nombre_producto',
        'numero_lote',
        'nombre_sucursal',
        'tipo',
        'cantidad',
        'stock_anterior',
        'stock_posterior',
        'referencia',
        'clave_operacion',
        'ocurrido_en',
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'stock_anterior' => 'integer',
        'stock_posterior' => 'integer',
        'ocurrido_en' => 'datetime',
    ];
}
