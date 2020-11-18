<?php

namespace App\Http\Controllers\Vacation\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;

//  ログインユーザと操作対象ユーザが一致するか確認するミドルウェア
//
class is_user
{
    public function handle($request, Closure $next)
    {
    //  dump( auth('user')->user()->is_user(), optional( auth('admin')->user())->is_admin() );   
        if(  optional( auth('admin')->user())->is_admin() ) { 
            Auth::guard('admin')->logout();
            redirect()->route( 'user.login');
            // abort( 403, 'middleware.is_user: 管理者は対象外です'); 
        
        }
        if( !optional( auth('user')->user())->is_user()  ) { abort( 403, 'middleware.is_user: アクセス権がありません'); }

        return $next($request);
    }
}
