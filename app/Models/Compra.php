<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Inventario;

class Compra extends Model
{
    protected $table = 'compras';

    protected $fillable = [
        'sucursal_id',
        'proveedor_id',
        'usuario_id',
        'fecha_compra',
        'total',
        'estado',
        'comprobante',
        'nota',
    ];

    protected $casts = [
        'fecha_compra' => 'datetime',
        'total' => 'decimal:2',
    ];

    //relaciones

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function detalles()
    {
        return $this->hasMany(CompraDetalle::class, 'compra_id');
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'compra_id');
    }
}
