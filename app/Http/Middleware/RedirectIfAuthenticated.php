<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if ($request->expectsJson()){
                return Response::json(['code'=>1,'msg'=>'用户已登录']);
            }else{
                if ($guard=='backend'){
                    return redirect('/backend');
                }elseif ($guard=='frontend'){
                    return redirect('/frontend');
                }
            }
            return redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }
}
