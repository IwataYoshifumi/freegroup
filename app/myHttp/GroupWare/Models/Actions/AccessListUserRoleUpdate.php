<?php
namespace App\myHttp\GroupWare\Models\Actions;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\AccessListUserRole;
use App\myHttp\GroupWare\Models\ACL;

use App\myHttp\GroupWare\Models\Search\ListOfUsersInTheAccessList;

class AccessListUserRoleUpdate  {
    
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //
    // AccessListUserRole DBの更新メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    
    /*
     *
     * AccessList のACL変更時に AccessListUserRole DBを更新する
     *
     */
    public static function update( $access_list ) {
        return self::AccessList( $access_list );
    }
    public static function AccessList( $access_list ) {

        if( $access_list instanceof AccessList ) {
            $access_list_id = $access_list->id;
        } elseif( is_numeric( $access_list )) {
            $access_list_id = $access_list;
            $access_list    = AccessList::find( $access_list_id );
        } else {
            die( __METHOD__ );
        }

        $ListOfUsersInTheAccessList = new ListOfUsersInTheAccessList( $access_list );

        $array = [];  
        $array_role = ACL::get_array_roles_for_select();
        foreach( $array_role as $role => $role_name ) {
            if( ! $role ) { continue; }

            $users = $role ."s";
            foreach( $ListOfUsersInTheAccessList->$users as $i => $user_id )  {
                array_push( $array, [ 'access_list_id' => $access_list->id, 'user_id' => $user_id, 'role' => $role ]);
            }
        }      
        
               DB::table( AccessListUserRole::table_name() )->where( 'access_list_id', $access_list->id )->delete();
        return DB::table( AccessListUserRole::table_name() )->insert( $array );
    }
    public static function AccessLists( $access_lists ) {
        foreach( $access_lists as $access_list_id ) {
            self::AccessList( AccessList::find( $access_list_id ));
        }
    }
    /*
     *
     * User が変更されたとき、AccessListUserRole DBを更新する
     * （部署異動、退職、復職時）
     *
     */
    public static function withUser( $user ) {
        return self::User( $user );
    }
    public static function User( $user ) {
        
        if( $user instanceof User ) {
            $user_id = $user->id;
        } elseif( is_numeric( $user )) {
            $user_id = $user;
            $user = User::find( $user_id );
        } else {
            die( __METHOD__ );
        }

        $subquery1 = $user->acls()->select( 'access_list_id' );
        $subquery2 = Dept::find( $user->dept_id )->acls()->select( 'access_list_id' );
        $subquery3 = ACL::where( 'aclable_type', Group::class )->whereIn( 'aclable_id', $user->groups()->select('id') )->select( 'access_list_id' );

        $subquery22 = AccessList::whereIn( 'id', $subquery2 );
        $subquery33 = AccessList::whereIn( 'id', $subquery3 );
        
        $access_lists = AccessList::whereIn( 'id', $subquery1 )
                                    ->union( $subquery22 )
                                    ->union( $subquery33 )
                                    ->get();
        $array = $access_lists->pluck('id')->toArray();
        return self::AccessLists( $array );
    }
    /*
     *
     * Group が変更されたとき、AccessListUserRole DBを更新する
     *
     */
    public static function withGroup( $group ) {
        return self::Group( $group );
    }
    public static function Group( $group ) {
        if( $group instanceof Group ) {
            $group_id = $group->id;
        } elseif( is_numeric( $group )) {
            $group_id = $group;
            $group = Group::find( $group_id );
        } else {
            die( __METHOD__ );
        }
        
        $access_lists = AccessList::whereGroup( $group );
        $array = $access_lists->get()->pluck('id')->toArray();

        return self::AccessLists( $array );
    }
    /*
     *
     * 部署異動があったときに、AccessListUserRole DBを更新する
     *
     */
    public static function Dept( $dept ) {
        
        if( $dept instanceof Dept ) {
            $dept_id = $dept->id;
        } elseif( is_numeric( $dept )) {
            $dept_id = $dept;
            $dept = Dept::find( $dept_id );
        } else {
            die( __METHOD__ );
        }

        $access_lists = AccessList::HaveDept( $dept )->get();
        $array = $access_lists->pluck('id')->toArray();

        return self::AccessLists( $array );
    }
    public static function Depts( $array ) {
        $access_lists = ACL::select( 'access_list_id' )
                           ->where(  'aclable_type', Dept::class )
                           ->whereIn( 'aclable_id', $array )
                           ->get();
        $array = $access_lists->pluck('access_list_id')->toArray();
        return self::AccessLists( $array );
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //
    // AccessListに対応する AccessListUserRole DBを削除
    //
    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public static function delete( AccessList $access_list ) {
        DB::table( AccessListUserRole::get_table_name() )->where( 'access_list_id', $access_list->id )->delete();
    }
    
    
    
    
}