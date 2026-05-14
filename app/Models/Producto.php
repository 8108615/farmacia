<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class);
    }

    public function formaFarmaceutica()
    {
        return $this->belongsTo(FormaFarmaceutica::class);
    }

    public function presentacion()
    {
        return $this->belongsTo(Presentacion::class);
    }
}
