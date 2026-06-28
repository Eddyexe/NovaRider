<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleOrdenTrabajo extends Model
{
    protected $table = 'TDetallesOrdenTrabajo';
    protected $primaryKey = 'id_detalle_ot';
    public $timestamps = false;

    protected $fillable = [
        'id_orden',
        'id_servicio',
        'descripcion',
        'cantidad',
        'precio_labor',
        'subtotal',
        'estadoA',
        'usuarioA',
        'fechahoraA'
    ];

    public function orden()
    {
        return $this->belongsTo(OrdenTrabajo::class, 'id_orden');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio');
    }
}
