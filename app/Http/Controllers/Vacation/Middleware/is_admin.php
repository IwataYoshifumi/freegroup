<?php

namespace App\Http\Controllers\Vacation\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;

//  管理者であるか確認するミドルウェア
//
class is_admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // dd( auth()->user() );
        // dd( optional( auth('user') ->user())->is_admin(), optional( auth('user')->user())->is_user(),auth()->user() );
        if( ! optional( auth( 'admin' )->user())->is_admin() ) { abort( '403', '管理者権限がありません(AA 1)' ); } 
        
        return $next($request);
    }
}
