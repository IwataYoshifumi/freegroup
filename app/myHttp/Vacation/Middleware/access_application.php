<?php

namespace App\Http\Controllers\Vacation\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;

class access_application
{
    //
    // 休暇申請データのアクセス権限
    // 許可ユーザは、管理者、申請者、承認者のみ
    //
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //　管理者は閲覧可能
        //
        if( optional(auth('admin')->user())->is_admin() ) { return $next( $request ); }
        
        //　未承認であれば、ログインフォームへ
        //
        if( !auth('user')->check() ) { return redirect(route('user.login')); }

        // 　申請者は閲覧可能
        //
        $user = optional(auth('user'))->user();
        if( is_null( $user )) { abort( 500, 'middleware.access_application : エラー0'); }
        
        $application = $request->route()->parameter('application');
        // dd( $application);
        if( $user->id == $application->user_id ) { return $next( $request ); }
        
        $approvals = $application->approvals;
        
        //　承認者は閲覧可能
        //
        foreach( $approvals as $app ) {
            // dump( $app->approver );
            if( $app->approver->id == $user->id ) { return $next( $request ); }

        }
        abort( 403, 'middleware:access_application: 権限がありません');
        
    }
}
