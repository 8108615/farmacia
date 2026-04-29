<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sucursal;
use App\Models\User;

class Empleado extends Model
{
    use HasFactory;
    protected $table = 'empleados';
    protected $fillable = [
        'usuario_id',
        'sucursal_id',
        'nombres',
        'apellidos',
        'tipo_doc',
        'numero_doc',
        'telefono',
        'direccion',
        'profesion',
        'fecha_nacimiento',
        'genero',
        'avatar',
        'estado',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }
}
