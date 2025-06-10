<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guest()) {
            return redirect()->route('login');
        } else {
            $auth_role_id = Auth::user()->getRoleId();
            if (! in_array($auth_role_id, [config('constant.ROLE_SUPER_ADMIN_ID')])) {
                abort(403);
            }
        }

        return $next($request);
    }
}
