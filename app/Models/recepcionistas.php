<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class recepcionistas extends Model
{
    use HasApiTokens, Notifiable;
    protected $fillable = [
        "nombre",
        "apellido",
        "documento",
        "telefono",
        "correo",
        "clave"
    ];

  
}

