<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class especialidades_medicos extends Model
{
    protected $fillable = [
        "id_especialidad",
        "id_medico"
    ];

    public function especialidades_medico(){
    return $this->belongsToMany(especialidades::class, "id_especialidad");
    }

    public function medico(){
    return $this->belongsToMany(medicos::class, "id_medico");
}
}
