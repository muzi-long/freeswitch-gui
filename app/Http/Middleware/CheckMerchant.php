<?php

namespace App\Http\Middleware;

use Closure;

class CheckMerchant
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
        $user = $request->user();
        if ($user != null){
            if (strtotime($user->merchant()->expire_at) < time()){
                abort(403, '商户已过期，请联系管理员续费');
            }
        }
        return $next($request);
    }
}
