<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class recepcionistas extends Model
{
    protected $fillable = [
        "nombre",
        "apellido",
        "documento",
        "telefono",
        "correo",
        "clave"
    ];
}