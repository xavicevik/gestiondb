<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\MilitanteResource;
use App\Http\Resources\ArchivoResource;
use App\Models\Archivo;
use App\Models\Militante;
use App\Models\Rol;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Validator;

class MilitanteController extends BaseController
{

    public function index()
    {
        $militantes = Militante::all();

        return $this->sendResponse(MilitanteResource::collection($militantes), 'Products retrieved successfully.');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $observaciones = 'Se ha creado el militante';
        $validator = Validator::make($input, [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'movil' => ['required', 'string', 'max:255'],
            'documento' => ['required', 'unique:militantes', 'string', 'max:255'],
            'idtipos_documento' => 'required|numeric|gt:0',
            'iddepartamento' => 'required|numeric|gt:0',
            'idciudad' => 'required|numeric|gt:0',
            'idinscripcion' => 'required|numeric|gt:0',
            'idgenero' => 'required|numeric',
            'idniveleducativo' => 'required|numeric',
            'idgrupoetnico' => 'required|numeric',
        ],
            [
                'nombre.required' => 'Ingrese el nombre',
                'apellido.required' => 'Ingrese el apellido',
                'email.required' => 'Ingrese el email',
                'movil.required' => 'Ingrese el teléfono celular',
                'documento.required' => 'Ingrese el número de identificacion',
                'documento.unique' => 'El documento ya existe',
                'idtipos_documento.numeric' => 'Seleccione un tipo de documento',
                'iddepartamento.numeric' => 'Seleccione un Departamento',
                'idciudad.numeric' => 'Seleccione una ciudad',
                'idinscripcion.numeric' => 'Seleccione la inscripción',
                'idgenero.numeric' => 'Seleccione un género',
                'idniveleducativo.numeric' => 'Seleccione el nivel educativo',
                'idgrupoetnico.numeric' => 'Seleccione un grupo étnico',
            ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $militante = Militante::create($input);
        $militante->password = Hash::make($militante->documento);
        $militante->estado = 3;
        $militante->changedpassword = null;
        $militante->username = $militante->documento;
        $militante->saveOrFail();
        $rol = Rol::where('id', 3)->first();
        $militante->assignRole($rol->nombre);

        \App\Http\Controllers\MilitanteController::setHistorial($militante->id, 1, $observaciones);

        return $this->sendResponse(new MilitanteResource($militante), 'Militante creado satisfactoriamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $militantes = Militante::find($id);

        if (is_null($militantes)) {
            return $this->sendError('Product not found.');
        }

        return $this->sendResponse(new MilitanteResource($militantes), 'Militante retrieved successfully.');
    }

    public function getMilitantebyDoc(Request $request)
    {
        $militante = Militante::where('documento', $request->documento)
                                ->with('genero')
                                ->with('niveleducativo')
                                ->with('grupoEtnico')
                                ->with('estados')
                                ->with('tipoinscripcion')
                                ->with('corporacion')
                                ->with('remplazo')
                                ->with('tipodocumento')
                                ->with('inscripcion')
                                ->with('pais')
                                ->with('departamento')
                                ->with('ciudad')
                                ->first();

        if (is_null($militante)) {
            return $this->sendError('Militante no encontrado');
        }

        return $this->sendResponse(new MilitanteResource($militante), 'Militante encontrado satisfactoriamente');
    }

    public function getMilitantebyId(Request $request)
    {
        $militante = Militante::where('id', $request->id)
            ->with('genero')
            ->with('niveleducativo')
            ->with('grupoEtnico')
            ->with('estados')
            ->with('tipoinscripcion')
            ->with('corporacion')
            ->with('remplazo')
            ->with('tipodocumento')
            ->with('inscripcion')
            ->with('pais')
            ->with('departamento')
            ->with('ciudad')
            ->first();

        if (is_null($militante)) {
            return $this->sendError('Militante no encontrado');
        }

        return $this->sendResponse(new MilitanteResource($militante), 'Militante encontrado satisfactoriamente');
    }

    public function getFilesbyMilitante(Request $request)
    {
        $archivos = Archivo::where('idmilitante', $request->idmilitante)->get();

        if (is_null($archivos)) {
            return $this->sendError('Product not found.');
        }

        return $this->sendResponse(ArchivoResource::collection($archivos), 'Products retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Militante $militante)
    {
        $input = $request->all();
        $observaciones = 'El militante ha sido actualizado';
        $validator = Validator::make($input, [
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'documento' => ['required', 'string', 'max:255'],
        ],
            [
                'nombre.required' => 'Ingrese el nombre',
                'email.required' => 'Ingrese el email',
                'documento.required' => 'Ingrese el número de identificacion',
            ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $militante->update($request->all());
        \App\Http\Controllers\MilitanteController::setHistorial($militante->id, 2, $observaciones);

        return $this->sendResponse(new MilitanteResource($militante), 'Militante actualizado satisfactoriamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Militante $militantes)
    {
        $militantes->delete();

        return $this->sendResponse([], 'Product deleted successfully.');
    }
}
