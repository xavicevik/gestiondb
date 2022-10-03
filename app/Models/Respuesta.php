<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respuesta extends Model
{
    protected $table = 'text_respuestas';
    protected $fillable =[
        'idexamenuser',
        'idpregunta',
        'idrespuesta',
        'estado'
    ];

    public function opciones() {
        return $this->hasMany(Opcion::class, 'idopcion');
    }
    public function examen(){
        return $this->belongsTo(Examen::class, 'idexamen');
    }
}
