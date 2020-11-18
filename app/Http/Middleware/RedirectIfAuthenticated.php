<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Support\Facades\Auth;

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
        // dump( $guard, Auth::guard($guard)->check() );
        if (Auth::guard($guard)->check()) {
            if( $guard === 'user' ) {
                return redirect( RouteServiceProvider::USER_HOME );
            } elseif( $guard === 'admin' ) {
                return redirect( RouteServiceProvider::ADMIN_HOME );
            } else {
                return redirect(RouteServiceProvider::HOME);
            }
        }
        // dd( $next, $guard, $request );
        return $next($request);
    }
}
