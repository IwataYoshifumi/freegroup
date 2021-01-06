<?php

namespace App\myHttp\GroupWare\Controllers\Search;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;


use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\AccessListUserRole;
use App\myHttp\GroupWare\Models\Actions\AccessListAction;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\Schedule;


class SearchSchedule {
    
    
    static public function search( Request $request ) {
        
        $find = new valuesForSearchingSchedules( $request );
        
        //　期間の検索（月、週、日）
        //
        $schedules = Schedule
            //->selectRaw(  ' \'作成者\' as tag ,  id, start_time, end_time, user_id, name' )
            // ->where( 'user_id', 2 )
            ::where( function( $sub_query ) use ( $find ) {
                        $sub_query->where( function( $query ) use ( $find ) {
                                    $query->where( 'start_date', '>=', $find->start_date )
                                          ->where( 'start_date', '<=', $find->end_date   );
                                    });
                        $sub_query->orWhere( function( $query ) use( $find ) {
                                    $query->where( 'end_date', '>=', $find->start_date )
                                          ->where( 'end_date', '<=', $find->end_date   );
                                    });
                        $sub_query->orWhere( function( $query ) use( $find ) {
                                    $query->where( 'start_date', '<', $find->start_date )
                                          ->where( 'end_date',   '>', $find->end_date   );
                                    });
            });

        
        // dump( $find, $schedules->get()->toArray() );

        return $schedules->get();
    }
    /*
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //　検索する
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    
    //　作成した人、関連者をまとめて検索
    //
    //  $search_mode = 0  スケジュール作成者のみを検索
    //  $search_mode = 1  スケジュール関連者のみを検索
    //  $search_mode = 2  スケジュール作成者・関連者を検索（関連者は重複削除）
    //  
    //  $schedules :: 作成者を検索
    //  $schedules :: 関連者を検索
    
    static public function search_1( $find, $search_mode = null, $sort = null, $asc_desc = null ) {
        // dump( $find );
        $start_date = Carbon::parse( $find['start_date'] )->format( 'Y-m-d 00:00:00' );
        $end_date   = Carbon::parse( $find['end_date']   )->format( 'Y-m-d 23:59:59' );
        
        $schedules = Schedule
                    //->selectRaw(  ' \'作成者\' as tag ,  id, start_time, end_time, user_id, name' )
                    // ->where( 'user_id', 2 )
                    ::where( function( $sub_query ) use ( $start_date, $end_date ) {
                                $sub_query->where( function( $query ) use ( $start_date, $end_date ) {
                                            $query->where( 'start_time', '>=', $start_date )
                                                  ->where( 'start_time', '<=', $end_date   );
                                            });
                                $sub_query->orWhere( function( $query ) use( $start_date, $end_date ) {
                                            $query->where( 'end_time', '>=', $start_date )
                                                  ->where( 'end_time', '<=', $end_date   );
                                            });
                                $sub_query->orWhere( function( $query ) use( $start_date, $end_date) {
                                            $query->where( 'start_time', '<', $start_date )
                                                  ->where( 'end_time',   '>', $end_date   );
                                            });
                    });

        $schedules2 = clone $schedules;

        // 件名検索
        if( ! empty( optional( $find )['name'] )) {
            $find_name = "%".$find['name']."%";
            $schedules  = $schedules ->where( 'name', 'like', $find_name );
            $schedules2 = $schedules2->where( 'name', 'like', $find_name );
        }

        //  日報　あり・なし
        //
        // dump( $find );
        if( ! empty( $find['has_reports'])) {
            // dump( $find );
            if( $find['has_reports'] == 1 ) {
                //　日報あり
                //
                $schedules = $schedules->has( 'reports' );
                $schedules2= $schedules2->has( 'reports');
            } elseif( $find['has_reports'] == -1 ) {
                //  日報なし
                //
                $schedules = $schedules->doesntHave( 'reports' );
                $schedules2= $schedules2->doesntHave( 'reports');
            }
        }

        //　社員検索
        //
        if( array_key_exists( 'users', $find ) and is_array( $find['users'] ) and ! empty( $find['users'][0] )) {
            $schedules  = $schedules ->whereIn( 'user_id', $find['users']);
            
            $schedules2 = $schedules2->whereHas( 'users', function( $query ) use ( $find ) {
                                // $query->whereIn( 'user_id', $find['users'] );
                                $query->whereIn( 'scheduleable_id', $find['users'] );
                            });
            // dump( $schedules, $schedules2 );
        } else {
            //　部署検索
            //
            if( ! empty( $find['dept_id'] )) {
                //  dump( 'dept_id', $find['dept_id'] );
                
                $sub_query = DB::table( 'users' )->select( 'id' )->where( 'dept_id', $find['dept_id'] );
    
                $schedules  = $schedules ->whereIn( 'user_id', $sub_query );
                $schedules2 = $schedules2->whereHas( 'users', function( $query ) use ( $sub_query ) {
                        // $query->whereIn( 'user_id', $sub_query );
                        $query->whereIn( 'scheduleable_id', $sub_query );

                    });
                
            } else {
                
                //　部署検索もなければ、ログインＩＤで検索
                //
                $schedules  = $schedules ->where( 'user_id', auth('user')->id() );
                
                $schedules2 = $schedules2->whereHas( 'users', function( $query ) {
                                    // $query->where( 'user_id', auth('user')->id() );
                                        $query->where( 'scheduleable_id', auth('user')->id() );

                                });
                // dump( 'search login ID', auth('user')->id());
                // dump( 'search login ID', auth('user')->id(), $schedules, $schedules2 );

            }
        }
        
        // dump( $schedules, $schedules2 );
        
        //　検索実行
        //
        if( empty( $search_mode )) {
            //
            //　作成者ベースで検索
            //
            $returns = $schedules->selectRaw(  ' \'作成者\' as tag ,  id, name, place, start_time, end_time, period, notice, memo, user_id, schedule_type_id' )
                                 ->with(['user', 'schedule_type' ])->orderBy( 'start_time' )->get();
            // dump( 'search_mode 0');
        } elseif( $search_mode == 1 ) {
            //
            //  関連者ベースで検索
            //
            $returns = $schedules2->selectRaw(  ' \'関連者\' as tag ,  id, name, place, start_time, end_time, period, notice, memo, user_id, schedule_type_id' )
                                  ->with([ 'users', 'user', 'schedule_type' ])->orderBy( 'start_time' )->get();
            // dump( 'search_mode 1');
            // dump( $schedules2 );
        } elseif( $search_mode == 2 ) {
            //
            //  作成者・関連者両方で検索（関連者は重複削除）
            //
            // dump( 'search_mode 2 ');
            $sub_query = clone $schedules;

            $schedules2= $schedules2->selectRaw(  ' \'関連者\' as tag ,  id, name, place, start_time, end_time, period, notice, memo, user_id, schedule_type_id' )
                                    ->whereNotIn( 'id', $sub_query->select( 'id' ) )
                                    ->with(['users', 'user', 'schedule_type' ]);

            $schedules = $schedules ->selectRaw(  ' \'作成者\' as tag ,  id, name, place, start_time, end_time, period, notice, memo, user_id, schedule_type_id' )
                                    ->with([ 'user', 'schedule_type' ]);
                                    
            $returns = $schedules->union( $schedules2 )->orderBy( 'start_time' )->get();        

            // $schedules = $schedules->with( 'user' )
            //                       ->union( $schedules2 )
            //                       ->with( 'users' )
            //                       ->orderBy( 'start_time' )->get();
        }

        // dump( $returns->all() );
        return $returns;
    }
    */
    
}

class valuesForSearchingSchedules {
    
    public $base_date;  // Cabon
    public $date_span;  // monthly, weekly, dayly
    
    public $start_date;  // string 
    public $start_time;  // Carbon 
    public $end_date;    // string
    public $end_time;   // Carbon

    
    public function __construct( Request $request ) {

        $this->date_span = ( isset( $request->date_span  )) ? $request->date_span : 'monthly';
        $this->base_date = ( isset( $request->base_date  )) ? new Carbon( $request->base_date ) : Carbon::today();
        
        if( $this->date_span == 'monthly' ) {
            $this->start_time = new Carbon('first day of ' . $this->base_date->format('Y-m'));
            $this->end_time   = new Carbon('last day of '  . $this->base_date->format('Y-m'));
            $this->start_date = $this->start_time->format( 'Y-m-d' );
            $this->end_date   = $this->end_time->format( 'Y-m-d' );
        }        

    }
    
}
