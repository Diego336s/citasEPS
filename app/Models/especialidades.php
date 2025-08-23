<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class especialidades extends Model
{
    protected $fillable = [
        "nombre"
    ];

    public function especialidades_medico()
    {
        return $this->belongsToMany(especialidades_medicos::class, "id_especialidad");
    }
}
