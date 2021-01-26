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
use App\myHttp\GroupWare\Models\Calendar;

use App\myHttp\GroupWare\Requests\AccessListRequest;
use App\myHttp\GroupWare\Requests\DeleteAccessListRequest;

class SearchCalender {
    
    public static function search( $find ) {
        
        if( ! is_array( $find ) or ! count( $find )) { return Calendar::all(); }
        
        $query = new Calendar;

        //  アクセスリスト権限検索
        //
        $ids = [];
        if( isset( op($find)['keyword'] ) and $find['user_id'] ) {
            if( $find['keyword'] == 'isOwner' ) {
                // if_debug( 'isOwner');
                $calendars = Calendar::getOwner( $find['user_id'] );
                $ids = toArray( $calendars, 'id' );
            } elseif( $find['keyword'] == 'canWrite' ) { 
                // if_debug( 'canWrite');
                $calendars = Calendar::getCanWrite( $find['user_id'] );
                $ids = toArray( $calendars, 'id' );                
            } elseif( $find['keyword'] == 'canRead' ) {
                // if_debug( 'canRead');
                $calendars = Calendar::getCanRead( $find['user_id'] );
                $ids = toArray( $calendars, 'id' );
            }
            // if_debug( 'ids', $ids );
            $query = $query->whereIn('id', $ids );
        }

        //　公開種別の検索
        //
        if( op( $find )['type'] ) {
            $query = $query->whereIn( 'type', $find['type']);
        }
        if( op( $find )['disabled'] ) { 
            $query = $query->where( 'disabled', 1 ); 
            
        } else {
            $query = $query->where( 'disabled', 0 ); 

            if( op( $find )['not_use']  ) { 
                $query = $query->where( 'not_use',  1 ); 
            }
        }       
        
        $calendars = $query->get();
        // if_debug( 'query', $query, $query->get() );        
        
        return $calendars;
    }

    
}
