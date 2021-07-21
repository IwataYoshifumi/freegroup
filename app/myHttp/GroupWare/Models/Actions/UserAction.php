<?php
namespace App\myHttp\GroupWare\Models\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;

use DB;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;

use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;

use App\myHttp\GroupWare\Events\UserCreateEvent;
use App\myHttp\GroupWare\Events\UserRetireEvent;
use App\myHttp\GroupWare\Events\UserReturnEvent;
use App\myHttp\GroupWare\Events\UserTransferDeptEvent;

use App\myHttp\GroupWare\Models\Initialization\InitUser;

class UserAction  {
    
    // protected $table = 'groups';
    
    //　検索
    //
    public static function creates( Request $request ) {
        return self::stores( $request );
    }
    
    public static function stores( Request $request ) {

        $user = DB::transaction( function() use ( $request ) {
            $user = new User();
            $user->name     = $request->name;
            $user->email    = $request->email;
            $user->dept_id  = $request->dept_id;
            $user->grade    = $request->grade;
            $user->password = Hash::make($request->password);
            $user->retired  = false;
            $user->save();
            
            //  配属部署が使われているアクセスリストを更新
            // 
            AccessListUserRoleUpdate::Dept( $user->dept_id );

            //　他のDB関連初期化
            //
            // InitUser::init( $user );
            InitUser::whenUserHasCreatedFirst( $user );
            
            return $user;
        });

        //　イベント発行（ユーザの初期化、アクセスリストの更新など）
        //
        event( new UserCreateEvent( $user ));

        return $user;
    }
    
    public static function updates( User $user, Request $request ) {

        $old_user = clone $user;
        $user = DB::transaction( function() use ( $user, $request, $old_user ) {
            
            $user->name            = $request->name;
            $user->email           = $request->email;
            $user->dept_id         = $request->dept_id;
            $user->grade           = $request->grade;
            $user->retired         = ( empty( $request->retired )) ? 0    : 1;
            $user->date_of_retired = ( empty( $request->retired )) ? null : $request->date_of_retired;
            if( ! empty( $request->password )) {
                $user->password = Hash::make( $request->password );
            }
            $user->save();
            
            // AccessListUserRole DBの更新
            //
            $init_user = false;
            if( $user->retired != $old_user->retired ) {
                AccessListUserRoleUpdate::User( $user );
                $init_user = true;
            }
            if( $user->dept_id != $old_user->dept_id ) {
                AccessListUserRoleUpdate::Depts( [$user->dept_id, $old_user->dept_id] );
                $init_user = true;
            }
            if( $init_user ) { InitUser::init( $user ); }
            
            
            return $user;
        });

        //　イベント発生（退社・復社・部署異動）　実質的な処理は未実装
        //
        if( $old_user->retired == false and $user->retired == true ) {
            event( new UserRetireEvent( $user ));
        } elseif( $old_user->retired == true and $user->retired == false ) {
            event( new UserReturnEvent( $user ));
        } elseif( $old_user->dept_id != $user->dept_id ) {
            event( new UserTransferDeptEvent( $user, $old_user->dept ));
        } 
        
        return $user;
    }
    
    public static function deletes( User $user ) {

        $user = DB::transaction( function() use ( $user ) {

            });
        
        return $user;
    }
    

}

