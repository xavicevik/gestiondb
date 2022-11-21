<?php

namespace App\Http\Controllers;

use App\Models\Boleta;
use App\Models\Configuración;
use App\Models\Corporacion;
use App\Models\Empresa;
use App\Models\Estado;
use App\Models\Estadocc;
use App\Models\Genero;
use App\Models\Grupoetnico;
use App\Models\Importstatus;
use App\Models\Loteria;
use App\Models\Niveleducativo;
use App\Models\Pais;
use App\Models\Rol;
use App\Models\Serie;
use App\Models\Terminosycondiciones;
use App\Models\Tipohistorial;
use App\Models\Tipoinscripcion;
use App\Models\Tiposarchivo;
use App\Models\TiposDocumento;
use App\Models\User;
use App\Models\Vendedor;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Redis;

class MasterController extends Controller
{
    const canPorPagina = 10;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function rolesIndex(Request $request)
    {
        $buscar = $request->buscar;
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

        if ($buscar == ''){
            $roles = Role::orderBy($sortBy, $sortOrder)
                        ->paginate(self::canPorPagina);
        } else {
            $roles = Role::orderBy($sortBy, $sortOrder)
                        ->where('name', 'like', '%'. $buscar . '%')
                        ->paginate(self::canPorPagina);
        }

        if ($request->has('ispage') && $request->ispage){
            return ['data' => $roles];
        } else {
            return Inertia::render('Masters/RolesIndex', ['data' => $roles]);
        }
    }

    public function rolesshow(Request $request)
    {
        $role = Role::find($request->id);
        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")
            ->where("role_has_permissions.role_id", $request->id)->paginate(10);

        return ['role' => $role, 'rolePermissions' => $rolePermissions];
    }

    public function rolesedit(Request $request)
    {
        $role = Role::find($request->id);
        $permission = Permission::paginate(100);
        $rolePermissions = DB::table("role_has_permissions")
                                ->where("role_has_permissions.role_id", $request->id)
                                ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
                                ->all();
        $rolepermtemp = [];
        $rolepermtemp2[] = [];
        if ($rolePermissions) {
            foreach ($rolePermissions as $p) {
                $rolepermtemp[] = $p;
            }
        }

        /*
        foreach ($permission as $p => $val) {
            if (isset($rolePermissions[$p])) {
                $rolepermtemp2[$p] = $rolePermissions[$p]?$rolePermissions[$p]:0;
            } else {
                $rolepermtemp2[$p] = 0;
            }
        }
        */

        return ['role' => $role,
                'permission' => $permission,
                'rolePermissions' => $rolePermissions,
                'rolePermissionsjson'=> $rolepermtemp,
                '_token' => csrf_token()
                ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function rolesupdate(Request $request)
    {
        $role = Role::where('id', $request->idrol)->first();
        $role->revokePermissionTo(Permission::all());

        foreach ($request->all() as $key => $value) {
            if ($key != 'idrol') {
                $permiso[] = Permission::where("id", $key)->get();
            }
        }
        $role->syncPermissions($permiso);

        return redirect()->back()->with('message', 'Permisos actualizados correctamente');
    }

    public function paisesIndex(Request $request)
    {
        $buscar = $request->buscar;
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

        if ($buscar == ''){
            $paises = Pais::orderBy($sortBy, $sortOrder)
                            ->with('departamentos.ciudades')
                            ->paginate(self::canPorPagina);
        } else {
            $paises = Pais::orderBy($sortBy, $sortOrder)
                            ->with('departamentos.ciudades')
                            ->where('nombre', 'like', '%'. $buscar . '%')
                            ->paginate(self::canPorPagina);
        }

        if ($request->has('ispage') && $request->ispage){
            return ['data' => $paises];
        } else {
            return Inertia::render('Masters/PaisesIndex', ['data' => $paises]);
        }
    }

    public function corporacionesIndex(Request $request)
    {
        $buscar = $request->buscar;
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

        if ($buscar == ''){
            $corporaciones = Corporacion::orderBy($sortBy, $sortOrder)
                ->paginate(self::canPorPagina);
        } else {
            $corporaciones = Corporacion::orderBy($sortBy, $sortOrder)
                ->where('nombre', 'like', '%'. $buscar . '%')
                ->paginate(self::canPorPagina);
        }

        if ($request->has('ispage') && $request->ispage){
            return ['data' => $corporaciones];
        } else {
            return Inertia::render('Masters/CorporacionesIndex', ['data' => $corporaciones]);
        }
    }

    public function empresasIndex(Request $request)
    {
        //if (!$request->ajax()) return redirect('/');
        $buscar = $request->buscar;
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

        if ($buscar == ''){
            $empresas = Empresa::orderBy($sortBy, $sortOrder)
                ->with('tipodocumento')
                ->with('ciudad')
                ->with('tipoempresa')
                ->paginate(self::canPorPagina);
        } else {
            $empresas = Empresa::orderBy($sortBy, $sortOrder)
                ->where('nombre', 'like', '%'. $buscar . '%')
                ->with('tipodocumento')
                ->with('ciudad')
                ->with('tipoempresa')
                ->paginate(self::canPorPagina);
        }

        if ($request->has('ispage') && $request->ispage){
            return ['data' => $empresas];
        } else {
            return Inertia::render('Masters/EmpresasIndex', ['data' => $empresas]);
        }
    }

    public function tipodocIndex(Request $request)
    {
        //if (!$request->ajax()) return redirect('/');
        $buscar = $request->buscar;
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

        if ($buscar == ''){
            $tipos = TiposDocumento::orderBy($sortBy, $sortOrder)
                ->paginate(self::canPorPagina);
        } else {
            $tipos = TiposDocumento::orderBy($sortBy, $sortOrder)
                ->where('nombre', 'like', '%'. $buscar . '%')
                ->paginate(self::canPorPagina);
        }

        if ($request->has('ispage') && $request->ispage){
            return ['data' => $tipos];
        } else {
            return Inertia::render('Masters/TipodocIndex', ['data' => $tipos]);
        }
    }

    public function parametrosIndex(Request $request)
    {
        $buscar = $request->buscar;
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

        if ($buscar == ''){
            $data = Configuración::orderBy($sortBy, $sortOrder)
                ->paginate(self::canPorPagina);
        } else {
            $data = Configuración::orderBy($sortBy, $sortOrder)
                ->where('nombre', 'like', '%'. $buscar . '%')
                ->paginate(self::canPorPagina);
        }

        if ($request->has('ispage') && $request->ispage){
            return ['data' => $data];
        } else {
            return Inertia::render('Masters/ParametrosIndex', ['data' => $data]);
        }
    }

    public function generosIndex(Request $request)
    {
        $buscar = $request->buscar;
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

        if ($buscar == ''){
            $data = Genero::orderBy($sortBy, $sortOrder)
                ->paginate(self::canPorPagina);
        } else {
            $data = Genero::orderBy($sortBy, $sortOrder)
                ->where('nombre', 'like', '%'. $buscar . '%')
                ->paginate(self::canPorPagina);
        }

        if ($request->has('ispage') && $request->ispage){
            return ['data' => $data];
        } else {
            return Inertia::render('Masters/GenerosIndex', ['data' => $data]);
        }
    }

    public function etniasIndex(Request $request)
    {
        $buscar = $request->buscar;
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

        if ($buscar == ''){
            $data = Grupoetnico::orderBy($sortBy, $sortOrder)
                ->paginate(self::canPorPagina);
        } else {
            $data = Grupoetnico::orderBy($sortBy, $sortOrder)
                ->where('nombre', 'like', '%'. $buscar . '%')
                ->paginate(self::canPorPagina);
        }

        if ($request->has('ispage') && $request->ispage){
            return ['data' => $data];
        } else {
            return Inertia::render('Masters/EtniasIndex', ['data' => $data]);
        }
    }

    public function importIndex(Request $request)
    {
        $buscar = $request->buscar;
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

        if ($buscar == ''){
            $data = Importstatus::orderBy($sortBy, $sortOrder)
                ->paginate(self::canPorPagina);
        } else {
            $data = Importstatus::orderBy($sortBy, $sortOrder)
                ->where('nombre', 'like', '%'. $buscar . '%')
                ->paginate(self::canPorPagina);
        }

        if ($request->has('ispage') && $request->ispage){
            return ['data' => $data];
        } else {
            return Inertia::render('Masters/ImportIndex', ['data' => $data]);
        }
    }

    public function niveleducativoIndex(Request $request)
    {
        $buscar = $request->buscar;
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

        if ($buscar == ''){
            $data = Niveleducativo::orderBy($sortBy, $sortOrder)
                ->paginate(self::canPorPagina);
        } else {
            $data = Niveleducativo::orderBy($sortBy, $sortOrder)
                ->where('nombre', 'like', '%'. $buscar . '%')
                ->paginate(self::canPorPagina);
        }

        if ($request->has('ispage') && $request->ispage){
            return ['data' => $data];
        } else {
            return Inertia::render('Masters/NiveleducativoIndex', ['data' => $data]);
        }
    }

    public function tipohistorialIndex(Request $request)
    {
        $buscar = $request->buscar;
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

        if ($buscar == ''){
            $data = Tipohistorial::orderBy($sortBy, $sortOrder)
                ->paginate(self::canPorPagina);
        } else {
            $data = Tipohistorial::orderBy($sortBy, $sortOrder)
                ->where('nombre', 'like', '%'. $buscar . '%')
                ->paginate(self::canPorPagina);
        }

        if ($request->has('ispage') && $request->ispage){
            return ['data' => $data];
        } else {
            return Inertia::render('Masters/TipohistorialIndex', ['data' => $data]);
        }
    }

    public function tipoarchivosIndex(Request $request)
    {
        $buscar = $request->buscar;
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

        if ($buscar == ''){
            $data = Tiposarchivo::orderBy($sortBy, $sortOrder)
                ->paginate(self::canPorPagina);
        } else {
            $data = Tiposarchivo::orderBy($sortBy, $sortOrder)
                ->where('nombre', 'like', '%'. $buscar . '%')
                ->paginate(self::canPorPagina);
        }

        if ($request->has('ispage') && $request->ispage){
            return ['data' => $data];
        } else {
            return Inertia::render('Masters/TipoarchivosIndex', ['data' => $data]);
        }
    }

    public function tipodocSearch(Request $request)
    {
        //if (!$request->ajax()) return redirect('/');
        $buscar = $request->buscar;
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

        if ($buscar == ''){
            $tipos = TiposDocumento::orderBy($sortBy, $sortOrder)
                ->get();
        } else {
            $tipos = TiposDocumento::orderBy($sortBy, $sortOrder)
                ->where('nombre', 'like', '%'. $buscar . '%')
                ->get();
        }

        return ['data' => $tipos];
    }

    public function getRoles(Request $request)
    {
        if(Auth::user()->idrol == 1 || Auth::user()->idrol == 6) {
            $roles = Rol::all();
        } else {
            $roles = Rol::where('id', '<>', 1)
                        ->where('id', '<>', 6)
                        ->get();
        }
        return ['data' => $roles];
    }

    public function getEmpresas(Request $request)
    {
        $empresas = Empresa::all();

        return ['data' => $empresas];
    }

    public function inscripciones(Request $request)
    {
        $inscripciones =  Tipoinscripcion::all();

        return ['inscripciones' => $inscripciones];
    }

    public function generos(Request $request)
    {
        $generos =  Genero::all();

        return ['generos' => $generos];
    }

    public function niveleducativo(Request $request)
    {
        $niveleducativo =  Niveleducativo::all();

        return ['niveleducativo' => $niveleducativo];
    }

    public function gruposetnicos(Request $request)
    {
        $gruposetnicos =  Grupoetnico::all();

        return ['gruposetnicos' => $gruposetnicos];
    }

    public function getTipohistorial(Request $request)
    {
        $tipo =  Tipohistorial::all();

        return ['tipo' => $tipo];
    }

    public function estados(Request $request)
    {
        $estados =  Estado::all();

        return ['estados' => $estados];
    }

    public function estadoscc(Request $request)
    {
        $estados =  Estadocc::all();

        return ['estados' => $estados];
    }

    public function tiposarchivos(Request $request)
    {
        $tiposarchivos =  Tiposarchivo::all();

        return ['tiposarchivos' => $tiposarchivos];
    }

    public function corporaciones(Request $request)
    {
        $corporacion =  Corporacion::all();

        return ['corporacion' => $corporacion];
    }

    public function getDashboard(Request $request)
    {
        $boletasdisponibles = Boleta::where('1')->get()->count();;

        return ['terminos' => $boletasdisponibles];
    }

}
