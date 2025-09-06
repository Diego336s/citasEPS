<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class pacientes extends Model
{
    use HasApiTokens, Notifiable;
    protected $fillable = [
    "nombre",
    "apellido",
    "documento",
    "telefono",
    "fecha_nacimiento",
    "rh",
    "sexo",
    "nacionalidad",
    "correo",
    "clave"
    ];

    public function citas(){
    return $this->hasMany(citas::class, "id_paciente");
    }


}
