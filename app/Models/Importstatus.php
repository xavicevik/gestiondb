<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Importstatus extends Model
{
    protected $table = 'importstatus';
    protected $fillable =[
        'documento',
        'correo',
        'celular',
        'estado',
        'archivo',
        'mensaje'
    ];
}
