<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class medicos extends Model
{
    protected $fillable = [
        "nombre",
        "apellido",
        "documento",
        "telefono",
        "correo",
        "clave"
    ];


    public  function citas()
    {
        return $this->hasMany(citas::class, "id_medico");
    }
}
