<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompraTmp extends Model
{
    protected $table = 'compra_tmps';

    protected $fillable = [
        'usuario_id',
        'sucursal_id',
        'producto_id',
        'precio_compra_unidad',
        'precio_venta_unidad',
        'porcentaje_ganancia_unidad',
        'cantidad',
        'fecha_creacion',
        'estado',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'precio_compra_unidad' => 'decimal',
        'precio_venta_unidad' => 'decimal',
        'porcentaje_ganancia_unidad' => 'decimal',
    ];

    //relaciones

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
