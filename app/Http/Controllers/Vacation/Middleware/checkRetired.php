<?php

namespace App\Http\Middleware\myMiddleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\User;

//  退職ユーザはログイン不可
//
class checkRetired
{
    public function handle($request, Closure $next) 
    {
        $user = Auth::guard('user')->user();
        
        if( is_null( $user )) {
            $user = Auth::guard('admin')->user();
        }
        if( is_null( $user )) {
            redirect( route('/'));
        }
        
        if( $user->is_retired() ) {
            $user->logout();
            abort( 403, '退職済み( CR 1 )');
        }
        return $next($request);
    }
};
