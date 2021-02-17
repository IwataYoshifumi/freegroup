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
        
        //　検索対象のユーザを含むアクセスリストを検索
        //
        $access_lists = AccessList::whereHas( 'user_roles', function( $query ) use( $find ) {
            $query->where( 'user_id', $find['user_id']);
            if( is_array( op( $find )['role'] )) {
                $query->whereIn( 'role', $find['role'] );
            }
        });

        //　自分がアクセス権限のあるアクセスリストのみ検索
        //
        if( ! op( $find )['all'] ) {
            $access_lists->whereHas( 'user_roles', function( $query ) {
                    $query->where( 'user_id', user_id() );
            });
        }

        //　自分のアクセス権限をロード
        //
        $access_lists->with( [ 'user_roles' => function( $query ) {
                $query->where( 'user_id', user_id() );
            }]);
        
        $access_lists = $access_lists->get();
        return $access_lists;
    }

    
}
