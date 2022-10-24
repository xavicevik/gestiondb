<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\Cliente;
use App\Models\Militante;
use App\Models\Puntoventa;
use App\Models\User;
use App\Models\Vendedor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Symfony\Component\Console\Input\Input;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;
use App\Models\UserCode;


class LoginController extends Controller
{
    use PasswordValidationRules;

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $token = csrf_token();
        return Inertia::render('Auth/Logincustom', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            '_token' => $token
        ]);
    }

    public function indexVendedor()
    {
        $token = csrf_token();
        return Inertia::render('Auth/Loginvendedor', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            '_token' => $token
        ]);
    }

    public function authenticate(Request $request)
    {
        $token = csrf_token();
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ],
            [
                'password.required' => 'El password no cumple con las políticas establecidas'
            ]);
        $roluser = User::where('username', $credentials['username'])
                         ->select('id', 'username', 'password', 'idrol', 'changedpassword', 'idempresa');
        $rolmilitante = Militante::where('username', $credentials['username'])
                         ->select('id', 'username', 'password', DB::raw("(SELECT '3') as idrol"), 'changedpassword', 'id');

        $rol = $roluser->union($rolmilitante)->first();

        if (! is_null($rol) && is_null($rol->changedpassword)) {
            /*
            return redirect()->route('changepass.index', [
                'puntoventas' => $rol['empresa']['puntosventa'],
                'username' => $request->username,
                'password' => $request->password,
                '_token' => $token
            ]);

            */
            return Inertia::render('Auth/Cambiarpassword', [
                'puntoventas' => empty($puntosventa)?[]:$puntosventa,
                'username' => $request->username,
                'password' => $request->password,
                '_token' => $token
            ]);
        }

        if (! is_null($rol) && $rol->idrol == 3) {
            $guard = Auth::guard('militante');
            if ($guard->attempt($credentials, ($request->remember == 'on') ? true : false)) {
                $request->session()->regenerate();

                Session::put('user_2fa', $guard->user()->id);
                return redirect()->intended('examens/1');
            }
        }
        $guard = Auth::guard('web');
        if ($guard->attempt($credentials, ($request->remember == 'on') ? true : false)) {
            $request->session()->regenerate();

            if (Auth::user()->rut) {
                //auth()->user()->generateCode();
                auth()->user()->sendemail();

                return redirect()->route('2fa.index', ['_token' => csrf_token()]);
            } else {
                Session::put('user_2fa', Auth::user()->id);
                return redirect()->intended('militantes');
            }

        }

        return back()->withErrors([
            'username' => 'Las credenciales ingresadas no corresponden con un usuario registrado',
        ])->onlyInput('username');
    }

    public function changePassword(Request $request)
    {
        $token = csrf_token();
        return Inertia::render('Auth/Cambiarpassword', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            '_token' => $token
        ]);
    }


    /**
     * Validate and update the user's password.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function updatePassword(Request $request)
    {
        $mytime= Carbon::now('America/Bogota');
        $input = $request;

        $roluser = User::where('username', $input->username)
            ->with('empresa.puntosventa')
            ->select('id', 'username', 'password', 'idrol', 'changedpassword');
        $rolmilitante = Militante::where('username', $input->username)
            ->select('id', 'username', 'password', DB::raw("(SELECT '3') as idrol"), 'changedpassword');

        $user = $roluser->union($rolmilitante)->first();

        Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'password' => $this->passwordRules(),
        ],
            [
                'password.required' => 'Ingrese el password',
                'password.confirmed' => 'El password no es igual a su confirmación'
            ]
        )->after(function ($validator) use ($user, $input) {
            if (! isset($input->current_password) ) {
                $validator->errors()->add('current_password', __('Debe ingresar el password actual.'));
            }
            if ( Hash::check($input->password, $user->password)) {
                $validator->errors()->add('password', __('El password actual no puede ser igual al anterior.'));
            }
        })->validateWithBag('updatePassword');

        if ($user->idrol == 3) {
            $usermilitante = Militante::where('id', $user->id)->first();
            $usermilitante->password = Hash::make($input->password);
            $usermilitante->changedpassword = $mytime->toDateString();
            $usermilitante->save();
        } else {
            $user->password = Hash::make($input->password);
            $user->changedpassword = $mytime->toDateString();
            $user->save();
        }

        return redirect()->back()
            ->with('message', 'Se cambió la contraseña');
        //return redirect()->route('login');
    }

    public function updatePasswordsu(Request $request)
    {
        $mytime= Carbon::now('America/Bogota');
        $input = $request;

        if($request->tipouser == 'cliente') {
            $user = Cliente::where('id', $input->id)->first();
        } elseif($request->tipouser == 'vendedor') {
            $user = Vendedor::where('id', $input->id)->first();
        } else {
            $user = User::where('id', $input->id)->first();
        }

        Validator::make($request->all(), [
            'password' => $this->passwordRules(),
        ],
            [
                'password.required' => 'Ingrese un password',
                'password.confirmed' => 'El password no es igual'
            ]
        )->validateWithBag('updatePassword');

        $user->forceFill([
            'password' => Hash::make($input->password),
            'changedpassword' => $mytime->toDateString()
        ])->save();

        return redirect()->back()->with('message', 'Se cambia contraseña');
    }



}
