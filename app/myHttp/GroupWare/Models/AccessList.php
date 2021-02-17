<?php

namespace App\myHttp\GroupWare\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Exception;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\AccessListUserRole;
use App\myHttp\GroupWare\Models\Calendar;
// use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\File as MyFile;


use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;

class AccessList extends Model {
    
    protected $fillable = [ 'name', 'memo', ];
    
    //////////////////////////////////////////////////////////////////////////
    //
    //　リレーション定義
    //
    //////////////////////////////////////////////////////////////////////////



    public function groups() {
        return $this->morphedByMany( Group::class, 'accesslistable' );
    }
    
    public function calendars() {
        return $this->morphedByMany( calendar::class, 'accesslistable' );
    }
    
    public function report_lists() {          
        return $this->morphedByMany( ReportList::class, 'accesslistable' );
    }

    public function files() {  
        return $this->morphedByMany( MyFile::class, 'accesslistable' );
    }
    
    public function acls() {
        return $this->hasMany( ACL::class, 'access_list_id', 'id' );
    }
    
    public function user_roles() {
        // return $this->hasMany( AccessListUserRole::class  );
        return $this->hasMany( AccessListUserRole::class, 'access_list_id' , 'id' );
    }

    public function accesslistables() {
        $groups    = $this->groups;
        $calendars = $this->calendars;
        $report_lists = $this->report_lists;

        return $groups->merge( $calendars )->merge( $report_lists );
    }


    //////////////////////////////////////////////////////////////////////////
    //
    // ACL検索メソッド
    //
    //////////////////////////////////////////////////////////////////////////

    public static function whereUser( $user ) {
        if( $user instanceof User ) {
            $user_id = $user->id;
        } elseif( is_numeric( $user )) {
            $user_id = $user;
            $user = User::find( $user_id );
        } else {
            die( __METHOD__ );
        }
        
        $subquery = ACL::select( 'access_list_id' )->where( 'aclable_type', User::class )->where( 'aclable_type', $user_id );
        $access_lists = AccessList::whereIn( 'id', $subquery );
        return $access_lists;
    }
    public static function whereGroup( Group $group ) {
        $subquery = $group->acls()->select('access_list_id');
        return AccessList::whereIn( 'id', $subquery );
    }
    public static function HaveDept( $dept ) {
        if( $dept instanceof Dept ) {
            $dept_id = $dept->id;
        } elseif( is_numeric( $user )) {
            $dept_id = $dept;
            $dept = Dept::find( $dept_id );
        } else {
            die( __METHOD__ );
        }
        
        $subquery = ACL::select( 'access_list_id' )->where( 'aclable_type', Dept::class )->where( 'aclable_type', $dept->id );
        return AccessList::whereIn( 'id', $subquery );
    }

    //////////////////////////////////////////////////////////////////////////
    //
    // DB クエリビルダー
    //
    //////////////////////////////////////////////////////////////////////////
    
    public static function whereOwner( $user ) {
        if( $user instanceof User ) {
            $user_id = $user->id;
        } elseif( is_numeric( $user )) {
            $user_id = $user;
            $user = User::find( $user_id );
        } else {
            die( __METHOD__ );
        }
        $subquery = AccessListUserRole::whereOwner( $user_id )->select('access_list_id');
        return AccessList::whereIn( 'id', $subquery );
    }
    
    public static function whereInOwners( $users ) {
        // $users = ( is_array( $users )) ? $users : $users->pluck( 'id', 'id' )->toArray();

        $subquery = AccessListUserRole::whereInOwners( $users )->select('access_list_id');
        return AccessList::whereIn( 'id', $subquery );
    }
    
    
    public static function whereWriter( $user ) {
        if( $user instanceof User ) {
            $user_id = $user->id;
        } elseif( is_numeric( $user )) {
            $user_id = $user;
            $user = User::find( $user_id );
        } else {
            die( __METHOD__ );
        }
        $subquery = AccessListUserRole::whereWriter( $user_id )->select('access_list_id');
        return AccessList::whereIn( 'id', $subquery );
    }
    public static function whereReader( $user ) {
        
        if( $user instanceof User ) {
            $user_id = $user->id;
        } elseif( is_numeric( $user )) {
            $user_id = $user;
            $user = User::find( $user_id );
        } else {
            die( __METHOD__ );
        }
        $subquery = AccessListUserRole::whereReader( $user_id )->select('access_list_id');
        return AccessList::whereIn( 'id', $subquery );
    }
    public static function whereCanWrite( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        
        $subquery = AccessListUserRole::whereCanWrite( $user_id )->select('access_list_id' );
        return AccessList::whereIn( 'id', $subquery );
        
    }
    
    public static function whereCanRead( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        
        $subquery = AccessListUserRole::whereCanRead( $user_id )->select('access_list_id' );
        return AccessList::whereIn( 'id', $subquery );
    
    }
    
    public static function whereInUsersCanRead( $users ) {
        // $subquery = AccessListUserRole::whereInUsersCanRead( $users )->select( 'access_list_id' );
        $subquery = AccessListUserRole::whereInUsersCanRead( $users )->get()->pluck('access_list_id')->toArray();
        return AccessList::whereIn( 'id', $subquery );
    }

    public static function whereInUsersCanWrite( $users ) {
        // $subquery = AccessListUserRole::whereInUsersCanWrite( $users )->select( 'access_list_id' );
        $subquery = AccessListUserRole::whereInUsersCanWrite( $users )->get()->pluck( 'access_list_id' )->toArray();
        return AccessList::whereIn( 'id', $subquery );
    }
    
    
    
    //////////////////////////////////////////////////////////////////////////
    //
    // アクセスリスト検索メソッド
    //
    //////////////////////////////////////////////////////////////////////////
    
    public static function whereFreeBusyReader( User $user ) {
        die( __FILE__ . __METHOD__ );
    }
    public static function getOwner( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        $result = AccessListUserRole::with( ['access_list'] )
                                    ->where( 'user_id', $user_id )
                                    ->where( 'role', 'owner' )
                                    ->get()->pluck('access_list');
        return $result;
    }
    public static function getCanWrite( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        $result = AccessListUserRole::with( ['access_list'] )
                                    ->where( 'user_id', $user_id )
                                    ->whereIn( 'role', ['owner', 'writer'] )
                                    ->get()->pluck('access_list');
        return $result;
    }
    public static function getCanRead( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        $result = AccessListUserRole::with( ['access_list'] )
                                    ->where( 'user_id', $user_id )
                                    ->whereIn( 'role', ['owner', 'writer', 'reader'] )
                                    ->get()->pluck('access_list');
        return $result;
    }

    public static function where_ACL_is_Model( $model ) {
        if( is_string( $model )) {
            $access_lists = AccessList::whereHas( 'acls', function( $query ) use ( $model ) {
                $query->where( 'aclable_type', $model );
            });
        } else {
            $access_lists = AccessList::whereHas( 'acls', function( $query ) use ( $model ) {
                         $query->where( 'aclable_type', get_class( $model ))
                               ->where( 'aclable_id',   $model->id );
                        })->get();
        }
        return $access_lists;
    }
    
    //　アクセスリストが使われていればtrue
    //
    public function isAttached() {
        $result = DB::table('accesslistables')->where( 'access_list_id', $this->id )->count();
        return $result >= 1;
    }
    
    //////////////////////////////////////////////////////////////////////////
    //
    // 権限者　取得メソッド
    //
    //////////////////////////////////////////////////////////////////////////
    public function owners() {
        die( __FILE__ . __METHOD__ );
    }
    
    public function writers() {
        die( __FILE__ . __METHOD__ );
    }
    
    public function readers() {
        die( __FILE__ . __METHOD__ );
    }
    
    //////////////////////////////////////////////////////////////////////////
    //
    // アクセスリスト権限確認メソッド
    //
    //////////////////////////////////////////////////////////////////////////
    public function isOwner( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        $result = AccessListUserRole::where( 'user_id', $user_id )
                                    ->where( 'role', 'owner' )
                                    ->where( 'access_list_id', $this->id )->count();
        return $result == 1;
    }
    public function isWriter( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        $result = AccessListUserRole::where( 'user_id', $user_id )
                                    ->where( 'role', 'writer' )
                                    ->where( 'access_list_id', $this->id )->count();
        return $result == 1;
    }
    public function isReader( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        $result = AccessListUserRole::where( 'user_id', $user_id )
                                    ->where( 'role', 'reader' )
                                    ->where( 'access_list_id', $this->id )->count();
        return $result == 1;
    }
    public function canWrite( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        $result = AccessListUserRole::where( 'user_id', $user_id )
                                    ->whereIn( 'role', [ 'owner', 'writer'] )
                                    ->where( 'access_list_id', $this->id )->count();
        return $result == 1;
    }
    public function canRead( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        $result = AccessListUserRole::where( 'user_id', $user_id )
                                    ->whereIn( 'role', [ 'owner', 'writer', 'reader'] )
                                    ->where( 'access_list_id', $this->id );
        // $r = clone $result;
        // if_debug( $r->get() );
        
        return $result->count() == 1;
    }
    
    //////////////////////////////////////////////////////////////////////////
    //
    //　各アクションメソッド
    //
    //////////////////////////////////////////////////////////////////////////
    public function updateAccessListUserRole() {
        return AccessListUserRoleUpdate::AccessList( $this );
    }
    
    public function update_access_list_user_role() {
        return $this->updateAccessListUserRole();
    }


    
    //////////////////////////////////////////////////////////////////////////
    //
    //　フォーム用　配列取得関数
    //
    //////////////////////////////////////////////////////////////////////////
    
    // アクセスリストのコレクションを渡すとセレクトフォーム用の配列を返す
    // 
    public static function toArrayWithoutEmpry( $access_lists ) {
        foreach( $access_lists as $access_list ) {
            array_push( $array, [ $access_list->id, $access_list->name ] );
        }
        return $array;
    }

    public static function toArrayWithEmpty( $access_lists ) {
        $array = [ "" => "" ];
        foreach( $access_lists as $access_list ) {
            array_push( $array, [ $access_list->id, $access_list->name ] );
        }
        return $array;
    }

    //  AccessListController@Edit で使う各フォームの初期値を取得
    //　AccessListController@Edit で使用
    //
    public function get_arrays_for_selector() { 
        
        $orders = [];
        $roles  = [ "" => "" ];
        $types  = [ "" => "" ];
        $users  = [ "" => "" ];
        $depts  = [ "" => "" ];
        $groups = [ "" => "" ];
        
        // if_debug( $this->acls );
        foreach( $this->acls as $acl ) {

            // if_debug( "$acl->order, $acl->role, $acl->type, $acl->aclable_type, $acl->aclable_id" );
            $order = $acl->order;
            $type  = $acl->aclable_type;

            $orders[$order] = $order;
            $roles[$order] = $acl->role;

            if( $type == User::class ) {
                $types[$order] = 'user';
                $users[$order] = $acl->aclable_id;
            } elseif( $type == Dept::class ) {
                $types[$order] = 'dept';
                $depts[$order] = $acl->aclable_id;
            } elseif( $type == Group::class ) {
                $types[$order]  = 'group';
                $groups[$order] = $acl->aclable_id;
                
            } else {
                throw new Exception( 'AccessList:get_arrays_for_selectors : Error 1 ');
            }
        }
        
        // 空の行を追加するための措置
        //
        $add_rows = 5;
        $last = last( $orders );
        for( $i = $last+1; $i <= $last + $add_rows; $i ++ ) {
            $orders[$i] = $i;    
        }
        
        return [ 
            'orders'=> $orders,
            'roles' => $roles, 
            'types' => $types,
            'users' => $users,
            'depts' => $depts,
            'groups'=> $groups,
            ];

    }

}