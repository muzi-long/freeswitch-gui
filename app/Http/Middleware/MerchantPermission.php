<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;

class MerchantPermission
{
    public function handle($request, Closure $next, $permission)
    {
        if (Auth::guard('merchant')->guest()) {
            throw UnauthorizedException::notLoggedIn();
        }

        if (Auth::guard('merchant')->user()->merchant_id==0) {
            return $next($request);
        }

        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        foreach ($permissions as $permission) {
            if (Auth::guard('merchant')->user()->can($permission)) {
                return $next($request);
            }
        }

        throw UnauthorizedException::forPermissions($permissions);
    }
}
