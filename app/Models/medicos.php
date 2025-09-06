<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


class medicos extends Model
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


    public  function citas()
    {
        return $this->hasMany(citas::class, "id_medico");
    }

   
 
    protected $hidden = [
        'password',
        'remember_token',
    ];

   
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
 
}
