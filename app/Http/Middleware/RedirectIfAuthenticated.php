<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $role_id = Auth::user()->getRoleId();
                if ($role_id == config('constant.ROLE_SUPER_ADMIN_ID')) {
                    return redirect()->route('admin.home');
                } elseif ($role_id == config('constant.ROLE_ADMIN_ID')) {
                    return redirect()->route('admin.home');
                } else {
                    return redirect()->route('user.outwards.index');
                }
            }
        }

        return $next($request);
    }
}
