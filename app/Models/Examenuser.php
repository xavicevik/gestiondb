<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examenuser extends Model
{
    protected $table = 'test_examenuser';
    protected $fillable =[
        'idexamen',
        'idmilitante',
        'calificacion',
        'respuestas',
        'estado'
    ];

    public function respuesta() {
        return $this->hasMany(Respuesta::class, 'idrespuesta');
    }
    public function examen(){
        return $this->belongsTo(Examen::class, 'idexamen');
    }
    public function miliatnte(){
        return $this->belongsTo(Militante::class, 'idmilitante');
    }
}
