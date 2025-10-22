<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodigoVerificacion extends Model
{
    protected $table = 'codigos_verificacion';

    protected $fillable = [
        'email',
        'codigo',
        'expira_en',
    ];

    public $timestamps = true;

    protected $dates = ['expira_en'];
}
