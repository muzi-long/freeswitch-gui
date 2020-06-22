<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Response;

class Authenticate extends Middleware
{

    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                config(['auth.defaults.guard'=>$guard]);
                return $this->auth->shouldUse($guard);
            }
        }

        $this->unauthenticated($request, $guards);
    }

    protected function unauthenticated($request, array $guards)
    {

        if ($request->expectsJson()){
            return Response::json(['code'=>1,'msg'=>'当前用户未登录']);
        }else{
            if (in_array('backend',$guards)){ //跳后台
                $url = route('backend.admin.login');
            }elseif (in_array('frontend',$guards)){ //跳前台
                $url = route('front.staff.login');
            }else{
                $url = null;
            }
            throw new AuthenticationException(
                'Unauthenticated.', $guards, $url
            );
        }
    }

}
