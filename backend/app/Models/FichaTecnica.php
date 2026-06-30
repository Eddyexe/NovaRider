<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class FichaTecnica extends Model
{

protected $table='TFichasTecnicas';

protected $primaryKey='id_ficha';

public $timestamps=false;


protected $fillable=[
'id_motocicleta',
'nro_chasis',
'nro_motor',
'color',
'cilindrada',
'estadoA'
];


public function motocicleta()
{
return $this->belongsTo(Motocicleta::class,'id_motocicleta');
}

}