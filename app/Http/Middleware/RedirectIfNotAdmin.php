<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null): Response
    {
        if (Auth::guest()) {
            return redirect()->route('login');
        } else {
            $auth_role_id = Auth::user()->getRoleId();
            if (in_array($auth_role_id, [config('constant.ROLE_USER_ID')])) {
                return redirect()->route('user.outwards.index');
            }
        }

        return $next($request);
    }
}
