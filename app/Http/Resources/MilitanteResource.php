<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MilitanteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'documento' => $this->documento,
            'email' => $this->documento,
            'username' => $this->documento,
            'tipodocumento' => $this->tipodocumento?$this->tipodocumento->nombre:'',
            'estados' => $this->estados->nombre,
            'direccion' => $this->documento,
            'pais' => $this->pais->nombre,
            'departamento' => $this->departamento?$this->departamento->nombre:'',
            'ciudad' => $this->ciudad?$this->ciudad->nombre:'',
            'observaciones' => $this->observaciones,
            'telefono' => $this->telefono,
            'movil' => $this->movil,
            'fechaingreso' => $this->fechaingreso,
            'inscripcion' => $this->inscripcion->nombre,
            'fechanacimiento' => $this->fechanacimiento,
            'genero' => $this->genero?$this->genero->nombre:'',
            'niveleducativo' => $this->niveleducativo?$this->niveleducativo->nombre:'',
            'discapacitado' => $this->discapacitado?'S':'N',
            'victimaconflicto' => $this->victimaconflicto?'S':'N',
            'grupoEtnico' => $this->grupoEtnico?$this->grupoEtnico->nombre:'',
            'lider' => $this->lider?'S':'N',
            'avalado' => $this->avalado?'S':'N',
            'corporacion' => $this->corporacion?$this->corporacion->nombre:'',
            'periodo' => $this->periodo,
            'electo' => $this->electo?'S':'N',
            'votos' => $this->votos,
            'coalicion' => $this->coalicion?'S':'N',
            'nombrecoalicion' => $this->nombrecoalicion,
            'renuncio' => $this->renuncio?'S':'N',
            'fecharenuncia' => $this->fecharenuncia,
            'remplazo' => $this->remplazo?$this->remplazo->documento:'',
            'aportes' => $this->aportes,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'twitter' => $this->twitter,
            'created_at' => $this->created_at->format('Y/m/d'),
            'updated_at' => $this->updated_at->format('Y/m/d'),
        ];
    }
}
