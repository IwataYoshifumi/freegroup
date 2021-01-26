<?php

namespace App\myHttp\GroupWare\Controllers\Search;

use Illuminate\Http\Request;
use DB;
use Arr;


use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\AccessListUserRole;
use App\myHttp\GroupWare\Models\Actions\AccessListAction;
use App\myHttp\GroupWare\Models\ACL;

use App\myHttp\GroupWare\Models\Search\GetAccessLists;

class SearchGroup {
    
    public static function search( $find ) {
        if( ! is_array( $find ) or ! count( $find )) { return []; }

        $groups = Group::with( [ 'access_lists' ] );
        //ユーザの所属するグループを検索
        //
        if( op( $find )['users'] ) {
            $group_ids = [];
            foreach( optional( $find )['users'] as $user_id ) {
                $ids = User::find( $user_id )->groups()->pluck('id')->toArray();
                array_push( $group_ids, $ids );
            }
            $group_ids = array_unique( Arr::flatten( $group_ids ));
            
            $groups = $groups->whereIn( 'id', $group_ids );
            // if_debug( 'groups', $groups );
        }

        //　アクセスリストの検索
        //
        if( optional( $find )['access_list'] ) {
            $find_access_list = $find['access_list'];
            
            if( optional( $find_access_list )['user_id'] ) {
                if( optional( $find_access_list )['role'] ) {
                    $access_lists = GetAccessLists::find( $find_access_list['user_id'], $find_access_list['role'] );
                } else {
                    $access_lists = GetAccessLists::user( $find_access_list['user_id'] );
                }
                $access_list_ids = $access_lists->pluck('access_list_id')->toArray();
                $query = DB::table( 'accesslistables' )
                         #->select( 'accesslistable_id' )
                         ->whereIn( 'access_list_id', $access_list_ids )
                         ->where( 'accesslistable_type', Group::class )
                         ->get();
                
                // if_debug( $access_list_ids, $query );
                
                $groups = $groups->whereIn( 'id', $query->pluck('accesslistable_id')->toArray() );
                
            }
        }
        
        // if_debug( $groups );
        $result = $groups->get();
        
        // if_debug( $result );
        // return Group::all();        
        return $result;
    }

    
}
