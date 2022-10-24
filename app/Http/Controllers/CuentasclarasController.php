<?php

namespace App\Http\Controllers;

use App\Exports\ClientesExport;
use App\Exports\MilitantesExport;
use App\Exports\UsersExport;
use App\Models\Archivo;
use App\Models\Audits;
use App\Models\Configuración;
use App\Models\Cuentasclara;
use App\Models\Estadocc;
use App\Models\Historial;
use App\Models\Imagen;
use App\Models\Militante;
use App\Models\Confcomision;
use App\Models\Puntoventa;
use App\Models\Rifa;
use App\Models\Rol;
use App\Models\Tiposarchivo;
use App\Models\User;
use App\Models\Vendedor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Laravel\Jetstream\Jetstream;
use Maatwebsite\Excel\Facades\Excel;
use OwenIt\Auditing\Models\Audit;
use Spatie\Permission\Models\Role;

use Illuminate\Support\Facades\Http;

class CuentasclarasController extends Controller
{
    const canPorPagina = 15;
    const nuCreacion = 1;
    const nuModificacion = 2;
    const nuSancion = 3;
    const nuEliminacion = 4;
    const nuRenuncia = 5;
    const nuRemplazo = 6;
    const nuAprobacion = 7;
    const nuSolicitudcc = 9;
    const nuReposicioncc = 10;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $buscar = $request->buscar;
        $filtros = json_decode($request->filtros);

        if ($request->has('sortBy') && $request->sortBy <> ''){
            $sortBy = $request->sortBy;
        } else {
            $sortBy = 'id';
        }

        if ($request->has('sortOrder') && $request->sortOrder <> ''){
            $sortOrder = $request->sortOrder;
        } else {
            $sortOrder = 'desc';
        }

        $militantes = Militante::orderBy($sortBy, $sortOrder)
                                ->with('genero')
                                ->with('tipoinscripcion')
                                ->with('niveleducativo')
                                ->with('grupoetnico')
                                ->with('departamento')
                                ->with('ciudad')
                                ->with('estados')
                                ->with('remplazo')
                                ->with('corporacion')
                                ->with('tipodocumento')
                                ->with('archivos.tipoarchivo');

        if ($buscar <> '') {
            $militantes = $militantes
                        ->where('nombre', 'like', '%'. $buscar . '%')
                        ->orWhere('apellido', 'like', '%'. $buscar . '%')
                        ->orWhere('email', 'like', '%'. $buscar . '%')
                        ->orWhere('documento', 'like', '%'. $buscar . '%');
        }

        if (!is_null($filtros)) {
            if(!is_null($filtros->fechainicio) && $filtros->fechainicio <> '' && $filtros->fechainicio <> null) {
                $militantes = $militantes->where('fechaingreso', '>=', $filtros->fechainicio);
            }
            if(!is_null($filtros->fechafin) && $filtros->fechafin <> '' && $filtros->fechafin <> null) {
                $militantes = $militantes->where('fechaingreso', '<=', $filtros->fechafin);
            }
            if (!is_null($filtros->documento) && $filtros->documento <> '') {
                $militantes = $militantes->where('documento', 'like', '%' . $filtros->documento . '%');
            }
            if (!is_null($filtros->nombre) && $filtros->nombre <> '') {
                $militantes = $militantes->where('nombre', 'like', '%' . $filtros->nombre . '%')
                                         ->orWhere('apellido', 'like', '%' . $filtros->nombre . '%');
            }
            if (!is_null($filtros->email) && $filtros->email <> '') {
                $militantes = $militantes->where('email', 'like', '%' . $filtros->email . '%');
            }
            if (!is_null($filtros->movil) && $filtros->movil <> '') {
                $militantes = $militantes->where('movil', 'like', '%' . $filtros->movil . '%');
            }
            if(!is_null($filtros->idciudad) && $filtros->idciudad <> '') {
                $ciudades = $filtros->idciudad;
                $militantes = $militantes->whereHas('ciudad', function($query) use ($ciudades) {
                                           $query->where('nombre', 'like', '%'.$ciudades.'%');
                });
            }
            if (!is_null($filtros->idinscripcion) && $filtros->idinscripcion <> '-' && $filtros->idinscripcion <> 0) {
                $militantes = $militantes->where('idinscripcion', $filtros->idinscripcion);
            }
            if (!is_null($filtros->idgenero) && $filtros->idgenero <> '-' && $filtros->idgenero <> 0) {
                $militantes = $militantes->where('idgenero', $filtros->idgenero);
            }
            if (!is_null($filtros->idgrupoetnico) && $filtros->idgrupoetnico <> '-' && $filtros->idgrupoetnico <> 0) {
                $militantes = $militantes->where('idgrupoetnico', $filtros->idgrupoetnico);
            }
            if (!is_null($filtros->idcorporacion) && $filtros->idcorporacion <> '-' && $filtros->idcorporacion <> 0 && $filtros->idcorporacion <> null) {
                $militantes = $militantes->where('idcorporacion', $filtros->idcorporacion);
            }
            if (!is_null($filtros->lider) && $filtros->lider <> '' && $filtros->lider <> '-') {
                $militantes = $militantes->where('lider', $filtros->lider);
            }
            if (!is_null($filtros->avalado) && $filtros->avalado <> '' && $filtros->avalado <> '-') {
                $militantes = $militantes->where('avalado', $filtros->avalado);
            }
            if (!is_null($filtros->electo) && $filtros->electo <> '' && $filtros->electo <> '-') {
                $militantes = $militantes->where('electo', $filtros->electo);
            }
            if (!is_null($filtros->estado) && $filtros->estado <> '' && $filtros->estado <> '-') {
                $militantes = $militantes->where('estado', $filtros->estado);
            }

            if (!is_null($filtros->examen) && $filtros->examen <> '' && $filtros->examen <> '-') {
                $militantes = $militantes->join('test_examenuser', 'militantes.id', '=', 'test_examenuser.idmilitante')
                                         ->where('test_examenuser.estado', $filtros->examen);
            }
        }

        $militantes = $militantes->paginate(self::canPorPagina);

        if ($request->has('ispage') && $request->ispage){
            return ['militantes' => $militantes];
        } else {
            return Inertia::render('Cuentasclaras/Index', ['militantes' => $militantes, '_token' => csrf_token()]);
        }
    }

    public function getHistorial(Request $request)
    {
        $historial = Historial::where('idmilitante', $request->idmilitante)
                        ->with('usuario')
                        ->with('tipo')
                        ->orderby('updated_at', 'desc')
                        ->paginate(self::canPorPagina);

        return ['historial' => $historial];
    }

    public function getArchivos(Request $request)
    {
        $archivos =  Archivo::where('idmilitante', $request->idmilitante)
                              ->with('tipoarchivo')
                              ->get();

        return ['archivos' => $archivos];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $observaciones = 'Se ha creado el militante';
        Validator::make($request->all(), [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'movil' => ['required', 'string', 'max:255'],
            'documento' => ['required', 'string', 'max:255'],
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
                'idtipos_documento.numeric' => 'Seleccione un tipo de documento',
                'iddepartamento.numeric' => 'Seleccione un Departamento',
                'idciudad.numeric' => 'Seleccione una ciudad',
                'idinscripcion.numeric' => 'Seleccione la inscripción',
                'idgenero.numeric' => 'Seleccione un género',
                'idniveleducativo.numeric' => 'Seleccione el nivel educativo',
                'idgrupoetnico.numeric' => 'Seleccione un grupo étnico',
            ])->validate();

        $militante = Militante::create($request->all());
        $militante->password = Hash::make($militante->password);
        $militante->estado = 3;
        $militante->changedpassword = null;
        $militante->username = $militante->documento;
        $militante->saveOrFail();

        $rol = Rol::where('id', 3)->first();
        $militante->assignRole($rol->nombre);

        $this->setHistorial($militante->id, self::nuCreacion, $observaciones);

        return redirect()->back()->with('message', 'Militante creado satisfactoriamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Militante $militante)
    {
        $estadorenuncia = $militante->renuncio;
        try{
            DB::beginTransaction();
            $observaciones = 'El militante ha sido actualizado';
            Validator::make($request->all(), [
                'nombre' => ['required', 'string', 'max:255'],
                'apellido' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'movil' => ['required', 'string', 'max:255'],
                'documento' => ['required', 'string', 'max:255'],
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
                    'idtipos_documento.numeric' => 'Seleccione un tipo de documento',
                    'iddepartamento.numeric' => 'Seleccione un Departamento',
                    'idciudad.numeric' => 'Seleccione una ciudad',
                    'idinscripcion.numeric' => 'Seleccione la inscripción',
                    'idgenero.numeric' => 'Seleccione un género',
                    'idniveleducativo.numeric' => 'Seleccione el nivel educativo',
                    'idgrupoetnico.numeric' => 'Seleccione un grupo étnico',
                ])->validate();

            $militante->update($request->all());
            $this->setHistorial($militante->id, self::nuModificacion, $observaciones);
            if ($militante->renuncio == 1 && $estadorenuncia == 0) {
                $this->setRenuncia($militante);
            }
            DB::commit();

            return redirect()->back()->with('message', 'Usuario modificado satisfactoriamente');

        } catch (Throwable $e){
            DB::rollBack();

            return redirect()->back()->with('message', 'Error');
        }
    }

    public function updateEstado(Request $request, Militante $militante)
    {
        try{
            DB::beginTransaction();

            if ($request->tipo == 'sancionar') {
                $tipo = self::nuSancion;
                $estado = 11;
            } elseif ($request->tipo == 'aprobar') {
                $tipo = self::nuAprobacion;
                $estado = 1;
            } elseif ($request->tipo == 'eliminar') {
                $tipo = self::nuEliminacion;
                $estado = 10;
            }

            $militante->estado = $estado;
            $militante->save();
            $this->setHistorial($militante->id, $tipo, $request->observaciones);
            DB::commit();

            return redirect()->back()->with('message', 'Usuario modificado satisfactoriamente');

        } catch (Throwable $e){
            DB::rollBack();

            return redirect()->back()->with('message', 'Erro');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->idrol == 2) {
            $user = Cliente::where('id', $request->id)->first();
        } elseif ($request->idrol == 5) {
            $user = Vendedor::where('id', $request->id)->first();
        } else {
            $user = User::where('id', $request->id)->first();
        }

        $user->estado = !$user->estado;
        $user->save();

        return redirect()->back()->with('message', 'Usuario modificado satisfactoriamente');
    }

    public function validaEntrega(Request $request)
    {
        $conf = Configuración::where('nombre', 'ccfechamax')->first();
        $horaconf = Carbon::create($conf->valorstr);
        $horaparam = Carbon::create($request->data);

        if ($horaparam <= $horaconf) {
            $isOportuno = true;
        } else {
            $isOportuno = false;
        }

        return ['isOportuno' => $isOportuno];
    }

    public function updateCuentasclaras(Request $request)
    {
        $form = json_decode($request->data);
        try{
            DB::beginTransaction();

            if ($request->tipo == 1) {
                $cuentasclaras = new Cuentasclara();
                $cuentasclaras->idmilitante = $request->idmilitante;
            } else {
                $cuentasclaras = Cuentasclara::where('idmilitante', $request->idmilitante)->first();
            }

            $cuentasclaras->estado = $request->tipo;
            if (isset($form->fechapresentacion)) $cuentasclaras->fechapresentacion = Carbon::create($form->fechapresentacion);
            if (isset($form->requerimientos)) $cuentasclaras->requerimientos = Carbon::create($form->requerimientos);
            if (isset($form->autorequerimiento)) $cuentasclaras->autorequerimiento = $form->autorequerimiento;
            if (isset($form->correccion)) $cuentasclaras->correccion = $form->correccion;
            if (isset($form->presentacorreccion)) $cuentasclaras->presentacorreccion = $form->presentacorreccion;
            if (isset($form->renuente)) $cuentasclaras->renuente = $form->renuente;
            if (isset($form->investigado)) $cuentasclaras->investigado = $form->investigado;
            if (isset($form->sancionado)) $cuentasclaras->sancionado = $form->sancionado;
            if (isset($form->recurso)) $cuentasclaras->recurso = $form->recurso;
            if (isset($form->resolucionpago)) $cuentasclaras->resolucionpago = $form->resolucionpago;
            if (isset($form->fecharesolucion)) $cuentasclaras->fecharesolucion = Carbon::create($form->fecharesolucion);
            if (isset($form->fechapago)) $cuentasclaras->fechapago = Carbon::create($form->fechapago);
            if (isset($form->observaciones)) $cuentasclaras->observaciones = $form->observaciones;

            $r = new Request();
            $r->data = $cuentasclaras->fechapresentacion;
            $attime = $this->validaEntrega($r);

            if ($attime['isOportuno']) {
                $cuentasclaras->ingresosgastos = true;
            } else {
                $cuentasclaras->ingresosgastos = false;
            }

            if ($cuentasclaras->estado >= 1) {
                if (is_null($cuentasclaras->fechapresentacion) || $cuentasclaras->fechapresentacion == null) {
                    $cuentasclaras->renuente = true;
                    $cuentasclaras->investigado = true;
                }
            }

            if ($cuentasclaras->estado >= 2) {
                if (is_null($cuentasclaras->requerimientos) || $cuentasclaras->requerimientos == null) {
                    $cuentasclaras->renuente = true;
                }
            }

            if ($cuentasclaras->estado >= 3) {
                if ($cuentasclaras->correccion && !$cuentasclaras->presentacorreccion) {
                    $cuentasclaras->renuente = true;
                    $cuentasclaras->investigado = true;
                }
            }

            $cuentasclaras->save();
            $observaciones = Estadocc::where('id', $request->tipo)->first()->nombre;
            MilitanteController::setHistorial($cuentasclaras->idmilitante, 9, $observaciones);

            DB::commit();
            return ['estado' => true, 'message' => 'Usuario modificado satisfactoriamente'];

        } catch (Throwable $e){
            DB::rollBack();

            return redirect()->back()->with('message', 'Error');
        }

    }

    public function archivoupload(Request $request) {
        try{
            DB::beginTransaction();

            $allowedfileExtension = ['pdf','jpg','png','docx', 'doc', 'xls', 'xlsx'];
            $codigo = 1;

            if(isset($request->file)){
                $file = $request->file;
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $check = in_array($extension, $allowedfileExtension);

                if($check) {
                    $cuentasclaras = Cuentasclara::where('id', $request->id)->first();
                    $archivo = new Archivo();
                    $archivo->idtipoarchivo = 5;
                    $archivo->nombre = $filename;
                    $archivo->idmilitante = $cuentasclaras->idmilitante;

                    $archivo->extension = $extension;
                    $filename = time(). '_' . $filename;
                    $path = $file->storeAs('archivos', $filename);

                    $archivo->url = Storage::url($path);

                    $archivo->tamaño = $file->getSize();
                    $archivo->save();

                    $cuentasclaras->idarchivo = $archivo->id;
                    $cuentasclaras->save();
                } else {
                    $codigo = -1;
                    $mensaje = 'La extensión de al menos un archivo no es permitida';
                }
            }

            if ($codigo == -1) {
                DB::rollBack();
            } else {
                DB::commit();
            }

            $mensaje = 'Archivo actualizado';
        } catch (Throwable $e){
            DB::rollBack();

            $codigo = -1;
            $mensaje = 'Se ha presentado un error';
        }
        return redirect()->back()->with('message', $mensaje);
    }

}
