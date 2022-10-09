<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuentasclara extends Model
{
    protected $table = 'cc_cuentasclaras';
    protected $fillable =[
        'idmilitante',
        'estado',
        'fecharegistro',
        'fechapresentacion',
        'ingresosgastos',
        'presentacion',
        'requerimientos',
        'autorequerimiento',
        'correccion',
        'presentacorreccion',
        'renuente',
        'investigado',
        'sancionado',
        'recurso',
        'resolucionpago',
        'fecharesolucion',
        'pagado',
        'idarchivo'
    ];

    public function archivo(){
        return $this->belongsTo(Archivo::class, 'idarchivo');
    }

    public function militante(){
        return $this->belongsTo(Militante::class, 'idmilitante');
    }

    public function estado(){
        return $this->belongsTo(Estadocc::class, 'estado');
    }

}
