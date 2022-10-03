<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pregunta extends Model
{
    protected $table = 'test_pregunta';
    protected $fillable =[
        'idexamen',
        'nombre',
        'estado',
        'preguntas',
        'idrespuesta'
    ];

    public function opciones() {
        return $this->hasMany(Opcion::class, 'idpregunta');
    }
    public function examen(){
        return $this->belongsTo(Examen::class, 'idexamen');
    }

    public function respuesta(){
        return $this->belongsTo(Opcion::class, 'idrespuesta');
    }
}
