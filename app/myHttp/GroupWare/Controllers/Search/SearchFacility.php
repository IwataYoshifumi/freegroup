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
use App\myHttp\GroupWare\Models\Facility;


class SearchFacility {
    
    public static function search( $find ) {
        
        if( ! is_array( $find ) or ! count( $find )) { return Facility::paginate( 5 ); }
        
        $query = new Facility;

        //  アクセスリスト権限検索
        //
        $ids = [];
        if( isset( op($find)['auth'] ) and $find['user_id'] ) {
            if( $find['auth'] == 'owner' ) {
                $facilities = Facility::getOwner( $find['user_id'] );
                $ids = toArray( $facilities, 'id' );
            } elseif( $find['auth'] == 'writer' ) { 
                $facilities = Facility::getCanWrite( $find['user_id'] );
                $ids = toArray( $facilities, 'id' );                
            } elseif( $find['auth'] == 'reader' ) {
                // if_debug( 'canRead');
                $facilities = Facility::getCanRead( $find['user_id'] );
                $ids = toArray( $facilities, 'id' );
            }
            if_debug( 'ids', $ids );
            $query = $query->whereIn('id', $ids );
        }

        //　公開種別の検索
        //
        if( op( $find )['type'] ) {
            $query = $query->whereIn( 'type', $find['type']);
        }

        //　設備無効の検索条件
        //
        if( op( $find )['disabled'] ) { 
            $query = $query->where( 'disabled', 1 ); 
        } else {
            $query = $query->where( 'disabled', 0 ); 
        }       
        
        $facilities = $query->get();
        if_debug( 'query', $query, $facilities );        
        
        return $facilities;
    }

    
}
