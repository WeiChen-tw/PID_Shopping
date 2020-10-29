<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
class CheckStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
       
       
        //If the status is not approved redirect to login 
        if(Auth::check() && Auth::user()->banned != 'N'){
            Auth::logout();
            return redirect('/login')->with('erro_login', '此帳號停權');
        }
        if(!Auth::check()){
            return redirect('/login')->with('erro_login', '此帳號停權');
        }
        return $next($request);
    }
}
