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
use App\myHttp\GroupWare\Models\ACL;

use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;

class AccessListAction  {
    
    // AccessListの新規作成
    public static function creates( Request $request ) {

        $access_list = DB::transaction( function() use ( $request ) {

            $access_list = new AccessList;
            $access_list->name = $request->name;
            $access_list->memo = $request->memo;
            $access_list->save();
            
            // ACL DBの更新
            self::update_acls( $access_list, $request );
            
            // AccessListUserRole DBの更新
            // $access_list->updateAccessListUserRole();
            AccessListUserRoleUpdate::AccessList( $access_list );
            
            return $access_list;
        });    
        return $access_list;
    }
    
    // AccessListの修正
    public static function updates( AccessList $access_list, Request $request ) {

        $access_list = DB::transaction( function() use ( $access_list, $request ) {

            $access_list->name = $request->name;
            $access_list->memo = $request->memo;
            $access_list->save();
            // ACL DBの更新
            self::update_acls( $access_list, $request );

            // AccessListUserRole DBの更新
            // $access_list->updateAccessListUserRole();
            AccessListUserRoleUpdate::AccessList( $access_list );

            return $access_list;
        });    
        return $access_list;
    }
    
    // AccessListの削除
    public static function deletes( $access_list ) { 

        $access_list = DB::transaction( function() use ( $access_list ) {
                // if_debug( $request->input() );

                // ACL DBの削除                
                ACL::where( 'access_list_id', $access_list->id )->delete();
                
                // AccessListUserRole DBの削除
                AccessListUserRoleUpdate::delete( $access_list );   

                // AccessListの削除
                $access_list->delete();

            });
        
        return true;
    }
    
    // AccessList のリレーションACLを更新( creates, updates で使用)
    //
    public static function update_acls( AccessList $access_list, Request $request ) {
        
        $orders= $request->orders;
        $roles = $request->roles;
        $users = $request->users;
        $depts = $request->depts;
        $groups = $request->groups;
        $types  = $request->types;
        
        ACL::where( 'access_list_id', $access_list->id )->delete();
    
        // dd( $orders, $roles, $types, $users, $groups, $depts );
        $acls = [];
        $m = 1;

        // 順序入れ替えのための処理
        // foreach( $orders as $j => $i ) {
        foreach( $roles as $i => $role ) {
            // if_debug( "$i, $role, $types[$i], $users[$i], $groups[$i], $depts[$i]" );
            $acl = new ACL;
            
            if( empty( $role )) { continue; }
            
            $acl->order = $m;
            $acl->role = $role;
            $acl->access_list_id = $access_list->id;
            if( $types[$i] == 'user' ) {
                // User::find( $users[$i] )->acls()->save( $acl );
                if( ! $users[$i] ) { continue; }
                $acl->aclable_type = User::class;
                $acl->aclable_id = $users[$i];

            } elseif( $types[$i] == 'dept' ) {
                // Dept::find( $depts[$i] )->acls()->save( $acl );
                if( ! $depts[$i] ) { continue; }

                $acl->aclable_type = Dept::class;
                $acl->aclable_id = $depts[$i];

            } elseif( $types[$i] == 'group' ) {
                if( ! $types[$i] ) { continue; }

                // Group::find( $groups[$i] )->acls()->save( $acl );
                $acl->aclable_type = Group::class;
                $acl->aclable_id = $groups[$i];
            }
            $acl->save();
            $m++;
        }
        
    }
}

