<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotUser
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
            if (in_array($auth_role_id, [config('constant.ROLE_SUPER_ADMIN_ID'), config('constant.ROLE_ADMIN_ID')])) {
                return redirect()->route('admin.home');
            }
        }

        return $next($request);
    }
}
