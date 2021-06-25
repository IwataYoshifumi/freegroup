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
use App\myHttp\GroupWare\Models\TaskList;

use App\myHttp\GroupWare\Requests\AccessListRequest;
use App\myHttp\GroupWare\Requests\DeleteAccessListRequest;

class SearchTaskList {
    
    public static function search( $find ) {
        
        if( ! is_array( $find ) or ! count( $find )) { return TaskList::paginate( 5 ); }
        
        $query = new TaskList;

        //  アクセスリスト権限検索
        //
        $ids = [];
        if( isset( op($find)['auth'] ) and $find['user_id'] ) {
            if( $find['auth'] == 'owner' ) {
                $tasklists = TaskList::getOwner( $find['user_id'] );
                $ids = toArray( $tasklists, 'id' );
            } elseif( $find['auth'] == 'writer' ) { 
                $tasklists = TaskList::getCanWrite( $find['user_id'] );
                $ids = toArray( $tasklists, 'id' );                
            } elseif( $find['auth'] == 'reader' ) {
                // if_debug( 'canRead');
                $tasklists = TaskList::getCanRead( $find['user_id'] );
                $ids = toArray( $tasklists, 'id' );
            }
            if_debug( 'ids', $ids );
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
        
        $tasklists = $query->get();
        if_debug( 'query', $query, $tasklists );        
        
        return $tasklists;
    }

    
}
