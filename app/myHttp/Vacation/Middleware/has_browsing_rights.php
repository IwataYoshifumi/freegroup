<?php

namespace App\Http\Controllers\Vacation\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Model\Vacation\User;
use App\Model\Vacation\Dept;

//　ユーザDBへの閲覧権限を確認するミドルウェア
//
// class userAuthorization
class has_browsing_rights
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next )
    {
        if( optional( auth( 'user' ))->user() ) { 
            $auth = auth('user')->user(); 
        } elseif( optional(auth('admin'))->user() ) {
            $auth = auth('admin')->user(); 
        } else {
            abort( 403, 'has_browsing_rights: 認証エラー' );
        }

        //　ユーザの特定
        //
        // ユーザルート系
        $user = optional( $request->route())->parameter('user');
        // 休暇ルート系（vacation)
        if( is_null( $user )) {
            $user = optional( $request->route())->parameter('vacation')->user;
        }
        
        // dd( $auth, $user );
    
        //　管理者はユーザデータの閲覧権限あり
        //
        if( $auth->is_admin() ) { return( $next( $request )); }
        
        //　管理者以外は閲覧権限を確認
        //
        switch( optional( $auth )->browsing ) {
            case( '自分のみ' ) :
                if( $auth->id != $user->id ) {
                    abort(403, 'has_browsing_rights: 閲覧権限がありません(UDA 1)');
                }
            break;
            
            case( '部内' ) : 
                $dept = $auth->department;
                // dd( $dept, $user );
                if( ! $auth->department->hasUser( $user )) {
                    // dd( '不正アクセス(2)、ログアウト＆リダイレクト' );
                    // redirect( '/home' );
                    abort(403, '閲覧権限がありません(UDA 2 )');
                }
                break;
            case( "全社" ) :
                break;
            default:
                abort( 403, 'has_browsing_rights: 権限エラー' );
        }
        return $next($request);
    }
}