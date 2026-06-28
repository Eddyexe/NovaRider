<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'TServicios';
    protected $primaryKey = 'id_servicio';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio_estimado',
        'estadoA',
        'usuarioA',
        'fechahoraA'
    ];
}
