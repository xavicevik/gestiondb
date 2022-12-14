<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{
    protected $table = 'test_examen';
    protected $fillable =[
        'nombre',
        'estado',
    ];

    public function listapreguntas() {
        return $this->hasMany(Pregunta::class, 'idexamen');
    }

}
