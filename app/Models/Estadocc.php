<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estadocc extends Model
{
    protected $table = 'cc_estados';
    protected $fillable =[
        'nombre',
    ];
}
