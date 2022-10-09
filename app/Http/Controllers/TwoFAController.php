<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Session;
use App\Models\Usercode;

class TwoFAController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function index()
    {
        $token = csrf_token();
        return Inertia::render('Auth/2fa', [
            '_token' => $token
        ]);
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function store(Request $request)
    {
        $request->validate([
            'code'=>'required',
        ]);

        $find = Usercode::where('user_id', auth()->user()->id)
            ->where('code', $request->code)
            ->where('updated_at', '>=', now()->subMinutes(2))
            ->first();

        if (!is_null($find)) {
            Session::put('user_2fa', auth()->user()->id);
            return redirect()->route('dashboard');
        }

        return back()->with('message', 'You entered wrong code.');
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function resend()
    {
        auth()->user()->generateCode();

        return back()->with('message', 'Se ha enviado un SMS con el código de ingreso.');
    }

    public function sendemail()
    {
        auth()->user()->sendemail();

        return back()->with('message', 'Se ha enviado un correo con el código de ingreso.');
    }
}
