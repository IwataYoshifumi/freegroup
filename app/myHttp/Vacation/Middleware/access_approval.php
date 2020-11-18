<?php

namespace App\Http\Controllers\Vacation\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Vacation\User;
use App\Dept;

class access_approval
{
    //
    // 承認データのアクセス権限
    // 許可ユーザは、管理者、承認者のみ
    // 申請者は見れなくてもよい
    //
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        //　管理者はアクセス可能
        //
        if( Auth::guard('admin')->check() ) { return $next( $request ); }

        //　ユーザログインしていなければログイン画面へ
        //
        if( ! Auth::guard('user')->check() ) { return redirect()->route( 'user.login' ); }
        
        $auth = optional(auth('user'))->user();
        if( ! $auth ) { abort( 403, 'middleware.access_approval: アクセスエラー'); }
        
        //　承認者はアクセス可能
        //
        $approval = $request->route()->parameter('approval');
        if( $auth->id == $approval->approver_id ) { return $next( $request ); }
                
        abort( 403, 'middleware.access_approval:アクセス権限がありません( AAR 1 )');
    }
}
