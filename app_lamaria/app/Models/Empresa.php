<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresa';

    protected $fillable = [
        'nombre_comercial',
        'razon_social',
        'rfc',
        'telefono',
        'numero_whatsapp',
        'email',
        'ubicacion',
        'horario_normal',
        'horario_especial',
        'descripcion',
        'logotipo',
        'sitio_web',
    ];
}
