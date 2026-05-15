<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'ci_nit',
        'nombres_apellidos',
        'email',
        'telefono',
    ];

    public $timestamps = false;
}
