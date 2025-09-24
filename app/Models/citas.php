<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class citas extends Model
{
    protected $fillable = [
    "descripcion",
   
    "id_medico",
    "id_paciente",
    "fecha",
    "hora_inicio",   
    "estado"
    
    ];

    public function paciente(){
        return $this->belongsTo(pacientes::class, "id_paciente");
    }

    public function medico(){
    return $this->belongsTo(medicos::class, "id_medico");
    }
}
