<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;

class Check2FA
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Session::has('user_2fa')) {
            Auth::guard('web')->logout();
            return redirect()->route('login.index');
        }

        return $next($request);
    }
}
