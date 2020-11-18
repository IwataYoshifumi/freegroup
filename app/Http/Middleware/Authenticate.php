<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
     
    protected $user_route  = "user.login";
    protected $admin_route = "admin.login";
     
     
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            if( Route::is( 'user.*' )) {
                return( route( $this->user_route ));
            } elseif( Route::is( 'admin.*' )) {
                // dd( $this, $request );
                return( route( $this->admin_route ));
            } else {
                return route('user.login');
            }
        }
    }
}
