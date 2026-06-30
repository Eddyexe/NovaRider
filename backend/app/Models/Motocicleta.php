<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Motocicleta extends Model
{
    protected $table = 'TMotocicletas';

    protected $primaryKey = 'id_motocicleta';

    public $timestamps = false;

    protected $fillable = [
        'id_cliente',
        'placa',
        'marca',
        'modelo',
        'anio',
        'nro_chasis',
        'nro_motor',
        'color',
        'cilindrada',
        'estadoA',
        'usuarioA',
        'fechahoraA',
    ];

    protected $casts = [
        'estadoA' => 'boolean',
        'anio' => 'integer',
        'fechahoraA' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }

    public function ordenesTrabajo()
    {
        return $this->hasMany(OrdenTrabajo::class, 'id_motocicleta');
    }
}