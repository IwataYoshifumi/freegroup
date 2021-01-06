<?php

namespace App\myHttp\GroupWare\Controllers\Search;

use Illuminate\Http\Request;
use DB;


use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\AccessListUserRole;
use App\myHttp\GroupWare\Models\Actions\AccessListAction;
use App\myHttp\GroupWare\Models\ACL;

use App\myHttp\GroupWare\Requests\AccessListRequest;
use App\myHttp\GroupWare\Requests\DeleteAccessListRequest;

class SearchAccessList {
    
    public static function search( $find ) {
        if( ! is_array( $find ) or ! count( $find )) { return []; }
        
        if( optional( $find )['all'] ) {
            //
            //　全てのアクセスリストを検索
            //
            $access_lists = AccessList::leftJoin( 'access_list_user_role as user_role', function( $join ) use ( $find ) {
                $join->on( 'access_lists.id', '=', 'user_role.access_list_id' )
                     ->where( 'user_role.user_id', $find['user_id'] );
            });
        } else {
            //
            //　自分に権限があるもののみ検索
            //
            $access_lists = AccessList::Join( 'access_list_user_role as user_role', 'access_lists.id', '=', 'user_role.access_list_id' );
            $access_lists->where( 'user_role.user_id', $find['user_id'] );
        }
        
        if( is_array( optional( $find )['role'] )) {
            $access_lists->whereIn( 'user_role.role', $find['role'] );
        }
        $access_lists = $access_lists->get();

        // dd( $access_lists );
        // foreach( $access_lists as $i => $list ) {
        //     $name = $list->name;
        //     dump( "$list->user_id, $list->role, $list->access_list_id, $name" );
        // }
        return $access_lists;
    }

    
}
