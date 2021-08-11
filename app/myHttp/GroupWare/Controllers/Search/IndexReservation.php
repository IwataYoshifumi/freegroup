<?php

namespace App\myHttp\GroupWare\Controllers\Search;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use DB;
use Carbon\Carbon;


use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\AccessListUserRole;
use App\myHttp\GroupWare\Models\Actions\AccessListAction;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\Reservation;
use App\myHttp\GroupWare\Models\Facitility;
use App\myHttp\GroupWare\Models\CalProp;



class IndexReservation {

    static public function search( Request $request ) {
        
        //　検索条件のチェック
        //
        if( ! isset( $request->start_date ) and ! isset( $request->end_date   )) { return []; }
        if( ! array( $request->facilities ) or  ! count( $request->facilities )) { return []; }
        // if( ! isset( $request->depts      ) and ! isset( $request->users    )) { return self::NULL_RETURN; }

        //　予約期間の検索
        //
        $start_time = new Carbon( $request->start_date . " 00:00:00" );
        $end_time   = new Carbon( $request->end_date   . " 23:59:59" );
        $reservations = Reservation::where( function( $sub_query ) use ( $start_time, $end_time ) {
                    $sub_query->where( function( $query ) use ( $start_time, $end_time ) {
                                $query->where( 'start', '>=', $start_time )
                                      ->where( 'start', '<=', $end_time   );
                                });
                    $sub_query->orWhere( function( $query ) use( $start_time, $end_time ) {
                                $query->where( 'end', '>=', $start_time )
                                      ->where( 'end', '<=', $end_time   );
                                });
                    $sub_query->orWhere( function( $query ) use( $start_time, $end_time ) {
                                $query->where( 'start', '<', $start_time )
                                      ->where( 'end',   '>', $end_time   );
                                });
                });
                
        //　キーワード検索（件名・備考検索）
        //
        if( ! empty( $request->key_word )) {
            $reservations->where( function( $query ) use( $request ) {
                $q = "%". $request->key_word . "%";
                $query->where(   'name', 'like', $q )
                      ->orWhere( 'memo', 'like', $q );
            });
        }
        //　社員、部署検索
        //
        $users = User::select( 'id' );
        
        if( is_array( $request->depts ) and is_array( $request->users )) {
                $users = $users->where( function( $query ) use ( $request ) {
                                $query->whereIn( 'dept_id', $request->depts )
                                      ->orWhereIn( 'id'   , $request->users );
                } );   
        } elseif( is_array( $request->depts ) and ! is_array( $request->users )) {
            $users = $users->whereIn( 'dept_id', $request->depts ); 
        
        } elseif( ! is_array( $request->depts ) and is_array( $request->users )) {
            $users = $users->whereIn( 'id',      $request->users ); 
        }
        
        //　作成者で検索
        //
        $reservations->whereIn( 'user_id', $users );
        
        //　対象設備の予定を検索
        //
        $reservations->whereIn( 'facility_id', $request->facilities );

        //　リレーションをロード
        //
        $reservations->with( [ 'user', 'facility' ] );
        
        $reservations = $reservations->get();
        if_debug( $reservations);
        return $reservations;
        
        
    }


}

