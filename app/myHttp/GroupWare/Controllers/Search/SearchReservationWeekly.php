<?php

namespace App\myHttp\GroupWare\Controllers\Search;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use DB;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\AccessListUserRole;
use App\myHttp\GroupWare\Models\Actions\AccessListAction;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\Reservation;
use App\myHttp\GroupWare\Models\Facility;

/*

　返値　下記の連想配列
 
 キー：日付（ 2021-06-10 ）
 　　　検索スパン分の日付の配列になる（monthly なら１か月分、weeklyなら７日分、dailyなら１日）
 
        日付配下も連想配列

 キー：multi_after 複数日の設備予約の２日目以降
 キー：multi この日が開始日の複数日設備予約
 キー：single この日が終日の設備予約
 キー：time この日の終日ではない設備予約

 値：Reservationクラス、Taskクラスのインスタンス
  それぞれのクラスは、Facility, CalProp, TaskList, TaskProp　をロード

 */

/*

    検索キーワード （ $requestの中の値　）

    base_date : 検索の記事日
    span      : 検索期間
        
    search_reservations : TRUEなら設備予約を検索する
    
    設備予約・タスクを検索する対象
    facilities   : 設備予約検索対象設備　　（検索フォームでチェックしたモノ、指定がなければ、ユーザがwritableの設備の設備予約を検索）
    
    設備・タスクリストの検索条件
    facility_permission  readable, writable, 

    返値
    
    returns['reservations']
    returns['facilities'] 　検索フォームに表示する設備のコレクション 
    未実装　returns['user_role_of_facilities'] : 各設備　へのユーザアクセス権限（キー facility_id, 値 owner, reader, writer
    未実装　returns['user_role_of_facilities'] : 各タスクリストへのユーザアクセス権限（キー tasllist_id, 値 owner, reader, writer
    
    //　下記は週次表示・日次表示で部署毎・社員毎に設備予約表示するためのデータ
    //
    returns['users']  設備予約・タスク作成者のリスト（キー　user_id, 値 Userのインスタンス）
    returns['depts'] 設備予約・タスク作成者の部署リスト（キー dept_id 値 Deptのインスタンス）
    returns['dept_user'] 作成者と所属部署の配列（　１次キー　dept_id 値　所属社員のuser_idの配列）
*/

class SearchReservationWeekly {
    
    public static function search( Request $request ) {
        
        //　検索対象期間を決定
        //
        $dates = self::calc_date( $request->base_date );
        
        //　検索条件の初期化
        //
        $start_date = Arr::first( $dates )->format( 'Y-m-d' );
        $end_date   = Arr::last(  $dates )->format( 'Y-m-d' );

        //　検査フォームに表示する設備を検索
        //
        $facilities = self::searchFacilities( $request );
        
        // if_debug( __METHOD__, $facilities );
        //　設備予約の検索
        //
        $reservations = self::searchReservations( $request, $dates );
        
        //　設備表示用データの作成
        //
        $returns = self::arrange_outputs( $dates, $reservations, $facilities );

        return $returns;
    }

    //　設備予約を検索
    //
    private static function searchReservations( Request $request, $dates ) {

        if( ! is_array( $request->facilities )) { return []; }

        //　検索条件の初期化
        //
        $start_date = Arr::first( $dates )->format( 'Y-m-d 00:00' );
        $end_date   = Arr::last(  $dates )->format( 'Y-m-d 23:59:59' );
        
        $facilities  = ( is_array( $request->facilities )) ? $request->facilities : [];
        
        if( count( $facilities ) == 0 ) { return []; }
        
        $reservations = Reservation::with( [ 'facility', 'user', 'user.dept' ] );
        $reservations = $reservations->orderBy( 'start' );

        //　予約期間で検索
        //
        $reservations = $reservations->where( function( $sub_query ) use ( $start_date, $end_date ) {
            $sub_query->where( function( $query ) use ( $start_date, $end_date ) {
                        $query->where( 'start', '>=', $start_date )
                              ->where( 'start', '<=', $end_date   );
                        });
            $sub_query->orWhere( function( $query ) use( $start_date, $end_date ) {
                        $query->where( 'end', '>=', $start_date )
                              ->where( 'end', '<=', $end_date   );
                        });
            $sub_query->orWhere( function( $query ) use( $start_date, $end_date ) {
                        $query->where( 'start', '<', $start_date )
                              ->where( 'end',   '>', $end_date   );
                        });
        });

        //　予約者を検索
        //
        if( $request->users and $request->depts ) {
            $sub_query = User::select('id')->whereIn( 'id', $request->users )
                                           ->orWhere( function( $query ) use ( $request ) {
                                                $query->whereIn( 'dept_id', $request->depts );
                                           });
            $reservations = $reservations->whereIn( 'user_id', $sub_query );
        } elseif( ! $request->users and $request->depts ) {
            $reservations = $reservations->whereHas( 'user', function( $query ) use ( $request ) {
                                        $query->whereIn( 'dept_id', $request->depts );
                            });
        } elseif( $request->users and ! $request->depts ) {
            $reservations = $reservations->whereIn( 'user_id', $request->users );
        }

        //　設備を検索
        //
        $reservations->whereIn( 'facility_id', $facilities );
        
        // ソート順
        //
        $reservations->orderBy( 'start', 'asc' )->orderBy( 'facility_id', 'asc' );
        // $reservations->orderBy( 'facility_id', 'asc' )->orderBy( 'start', 'asc' );
        $reservations = $reservations->get();

        // if_debug( $reservations->pluck( 'purpose')->toArray() );

        return $reservations;
    }
    
    //　設備を検索
    //
    public static function searchFacilities( Request $request ) {
        
        if( ! is_array( $request->facilities ) or ! count( $request->facilities )) { return []; }
        $facilities = Facility::whereIn( 'id', $request->facilities )
                              ->orderBy( 'category', 'asc' )
                              ->orderBy( 'sub_category', 'asc' )
                              ->orderBy( 'name', 'asc' )->get();
        // if_debug( __METHOD__, $facilities );
        return $facilities;
    }
    
    //　検索対象の期間（日付の配列を取得）
    //　月表示設備が四角になるように
    //　週初めを日曜日に統一させるため
    //
    //　返値
    //　$dates['Y-m-d']['H:i'] = Carbonインスタンス

    private static function calc_date( $base_date ) {
        
        $base_date = ( $base_date instanceof Carbon ) ? $base_date : new Carbon( $base_date );
        
        $first_date = $base_date->copy();
        $end_date   = $base_date->copy();

        //  週初めが日曜日
        //　週の終わりが土曜日
        //
        while( ! $first_date->isSunday() ) { $first_date->subDay(); }
        while( ! $end_date->isSaturday() ) { $end_date->addDay();  }

        //　月・週・日　表示カレンダーのデータ作成
        //
        $count = $first_date->diffInDays( $end_date );
        $dates = [];
        for ($i = 0; $i <= $count; $i++, $first_date->addDay()) {
            // copyしないと全部同じオブジェクトを入れてしまうことになる
            $dates[$first_date->format( 'Y-m-d' )] = $first_date->copy();
        }

        return $dates;
    }

    //　日付のマスの位置
    //
    private static function calc_date_col_and_row( $dates ) {
        
        $cols = [];
        $rows = [];
        
        $col = 1;
        $row = 1;
        foreach( $dates as $d => $date ) {
            $cols[$d] = $col;
            $rows[$d] = $row;
            
            if( $date->isSaturday() ) {
                $row++;
                $col = 1;
            } else {
                $col++;
            }
        }
        return [  $cols,  $rows ];
    }

    //
    //　出力データの整形
    //
    //  $returns['dates'] キーが日付テキスト、値がCarbon
    //  $returns['facilities'] キー：facility_id 値：設備のインスタンス
    //  $returns['multi']　２日間以上の設備予約の配列
    //  $returns['time'] １日以下の予定

    private static function arrange_outputs( $dates, $reservations,  $facilities ) {

        //　設備表示用データの作成
        //
        $start_date = Arr::first( $dates )->format( 'Y-m-d' );
        $end_date   = Arr::last(  $dates )->format( 'Y-m-d' );
        $returns = [ 'dates' => $dates, 
                     'facilities' => [],
                     'multi' => [], 
                     'time' => [], 
                     ];
        list( $returns['cols'], $returns['rows'] ) = self::calc_date_col_and_row( $dates );
        
        //　出力の初期化
        //
        foreach( $facilities as $facility ) {
            foreach( $dates as $date ) {
                $returns[$facility->id][$date->format('Y-m-d')] = [ 'multi' => [], 'time' => [], 'reservations' => [] ];
                $returns[$facility->id]['num_of_objects'][$date->format('Y-m-d')] = 0;
            }
            $returns['facilities'][$facility->id] = $facility;
        }
        
        foreach( $reservations as $reservation ) {

            $facility = $reservation->facility;

            //　設備予約の日数（１日以内 or 複数日か　)
            //
            $num_day = $reservation->getNumDates();
            $date = $reservation->start->copy();

            if( $num_day >= 2 ) {

                // if_debug( 'num_day 2', $reservation );
                //　設備予約が複数日の場合の処理
                //
                $start = 0;
                foreach( $dates as $date ) {
                    if( $start == 0 and ( $date->gte( $reservation->start ) or $date->format('Y-m-d') == $reservation->start->format('Y-m-d') )) {
                        $start = 1;
                        // array_push( $returns[$facility->id][$date->format('Y-m-d')]['multi']        , $reservation );
                        array_push( $returns[$facility->id][$date->format('Y-m-d')]['reservations'] , $reservation );
                        
                        $returns[$facility->id]['num_of_objects'][$date->format('Y-m-d')]++;
                        $order = $returns[$facility->id]['num_of_objects'][$date->format('Y-m-d')];

                        
                    } elseif( $start == 1 and ( $date->lte( $reservation->end ) or $date->format('Y-m-d') == $reservation->end->format('Y-m-d') )) {
                        // $returns[$facility->id]['num_of_objects'][$date->format('Y-m-d')]++;
                        if( $returns[$facility->id]['num_of_objects'][$date->format('Y-m-d')] + 1 > $order ) {
                            $returns[$facility->id]['num_of_objects'][$date->format('Y-m-d')]++;
                        } else {
                            $returns[$facility->id]['num_of_objects'][$date->format('Y-m-d')] = $order;                        
                        }
                        // array_push( $returns[$facility->id][$date->format('Y-m-d')]['multi'] , $reservation );
                    }
                }
            } else {
                //　設備予約が１日以内の場合
                //
                // if_debug( $reservation->facility->id, $reservation->purpose, $reservation->p_time() );
                $returns[$facility->id]['num_of_objects'][$reservation->start->format('Y-m-d')]++;                        
                // array_push( $returns[$facility->id][$reservation->start->format('Y-m-d')]['time'],         $reservation );
                array_push( $returns[$facility->id][$reservation->start->format('Y-m-d')]['reservations'], $reservation );

            }
        }
        
        //　設備ごとの　最大オブジェクト表示数
        //
        foreach( $facilities as $facility ) {
            $nums = $returns[$facility->id]['num_of_objects'];
            $returns[$facility->id]['max_num_of_objects'] = max( $nums );
        }
        
        if_debug( __METHOD__, $returns);
        
        return $returns;
    }
    
}

