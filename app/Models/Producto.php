<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function laboratorio()
    {
        return $this->belongsTo(laboratorio::class);
    }

    public function forma_farmaceutica()
    {
        return $this->belongsTo(FormaFarmaceutica::class);
    }

    public function presentacion()
    {
        return $this->belongsTo(Presentacion::class);
    }


}
