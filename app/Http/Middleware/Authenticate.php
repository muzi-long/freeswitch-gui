<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Response;

class Authenticate extends Middleware
{

    protected function unauthenticated($request, array $guards)
    {

        if ($request->expectsJson()){
            return Response::json(['code'=>1,'msg'=>'当前用户未登录']);
        }else{
            if (in_array('backend',$guards)){ //跳后台
                $url = route('backend.system.admin.login');
            }elseif (in_array('frontend',$guards)){ //跳前台
                $url = route('frontend.staff.login');
            }else{
                $url = null;
            }
            throw new AuthenticationException(
                'Unauthenticated.', $guards, $url
            );
        }
    }

}
