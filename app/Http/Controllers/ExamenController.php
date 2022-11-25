<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Exports\ClientesExport;
use App\Exports\MilitantesExport;
use App\Exports\UsersExport;
use App\Models\Archivo;
use App\Models\Audits;
use App\Models\Examenuser;
use App\Models\Historial;
use App\Models\Imagen;
use App\Models\Militante;
use App\Models\Confcomision;
use App\Models\Pregunta;
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

class ExamenController extends Controller
{
    const canPorPagina = 15;

    function __construct()
    {
        $this->middleware('permission:cursos-list', ['only' => ['show', 'evaluar', 'getExamen', 'putExamen']]);
    }

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

        $examenes = Examen::orderBy($sortBy, $sortOrder)
                           ->with('listapreguntas.respuesta')
                           ->with('listapreguntas.opciones');

        if ($buscar <> '') {
            $examenes = $examenes
                ->where('nombre', 'like', '%'. $buscar . '%')
                ->orWhere('documento', 'like', '%'. $buscar . '%');
        }

        $examenes = $examenes->paginate(self::canPorPagina);

        if ($request->has('ispage') && $request->ispage){
            return ['examenes' => $examenes];
        } else {
            return Inertia::render('Examens/Index', ['examenes' => $examenes, '_token' => csrf_token()]);
        }
    }

    public function getExamen(Request $request)
    {
        $user =  Auth::user();

        $examenuser = Examenuser::where('idmilitante', $request->idmilitante)
                                ->orderBy('updated_at', 'desc')
                                ->first();
        $examen = Examen::first();
        return [
                'examenuser' => $examenuser,
                'examen' => $examen,
                '_token' => csrf_token()
                ];
    }

    public function create()
    {
        //
    }

    public function putExamen(Request $request)
    {
        $resultados = $request->all();
        $correctas = 0;
        $incorrectas = 0;
        $user = Auth::user();

        foreach ($resultados as $val => $key) {
            $pregunta = Pregunta::where('id', $val)->first();
            $idrespuesta = $pregunta->idrespuesta;
            $idexamen = $pregunta->idexamen;

            if ($idrespuesta == $key) {
                $correctas++;
            } else {
                $incorrectas++;
            }
        }
        $examen = Examen::where('id', $idexamen)->first();
        $calificacion = round(100 * ($correctas/($examen->preguntas?$examen->preguntas:1)));
        $examenuser = new Examenuser();
        $examenuser->idmilitante = $user->id;
        $examenuser->idexamen = $idexamen;
        $examenuser->calificacion = $calificacion;
        $examenuser->respuestas = $correctas;
        $examenuser->estado = ($calificacion >= 70)?1:2;
        $examenuser->save();

        $preguntas = Pregunta::where('idexamen', $examen->id)
            ->inRandomOrder()
            ->limit($examen->preguntas)
            ->with('opciones')
            ->get();

        return Inertia::render('Examens/Show', [
                'examen' => $examen,
                'user' => $user,
                'arrayPreguntas' => $preguntas,
                'examenuser' => $examenuser,
                '_token' => csrf_token()]
        );
    }


    public function show(Examen $examen)
    {
        $user =  Auth::user();

        $examenuser = Examenuser::where('idexamen', $examen->id)
                                  ->where('idmilitante', $user->id)
                                  ->first();
        $preguntas = Pregunta::where('idexamen', $examen->id)
                            ->inRandomOrder()
                            ->limit($examen->preguntas)
                            ->with('opciones')
                            ->get();

        return Inertia::render('Examens/Show', [
                                                'examen' => $examen,
                                                'user' => $user,
                                                'arrayPreguntas' => $preguntas,
                                                'examenuser' => $examenuser,
                                                '_token' => csrf_token()]
                                                );
    }

    public function edit(User $user)
    {
        //
    }


    public function evaluar(Request $request) {
        $examen = Examen::where('id', $request->id)->first();
        $preguntas = Pregunta::where('idexamen', $examen->id)
                               ->inRandomOrder()
                               ->limit($examen->preguntas)
                               ->with('opciones')
                               ->get();

        dd($preguntas);

    }

}
