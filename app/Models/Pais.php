<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'paises';
    protected $fillable =[
        'nombre',
        'zona',
        'descripcion',
        'moneda'
    ];

    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'idpais');
    }
}
