<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opcion extends Model
{
    protected $table = 'text_opciones';
    protected $fillable =[
        'idpregunta',
        'nombre',
        'estado'
    ];

    public function examen(){
        return $this->belongsTo(Pregunta::class, 'idpregunta');
    }
}
