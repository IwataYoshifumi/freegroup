<?php

namespace App\Http\Controllers\Vacation\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;

//  ログインしているかどうか
//
class is_login
{
    public function handle($request, Closure $next)
    {
    //  dump( auth('user')->user()->is_user(), optional( auth('admin')->user())->is_admin() ); 
        if( optional( auth('user' )->user())->is_user()  ) { return  $next($request); }
        if( optional( auth('admin')->user())->is_admin() ) { return  $next($request); }
        abort( 403, 'middleware.is_login: ログインしてください。');
    }
}
