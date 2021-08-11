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

 キー：multi_after 複数日の予定の２日目以降
 キー：multi この日が開始日の複数日予定
 キー：single この日が終日の予定
 キー：task この日のタスク
 キー：time この日の終日ではない予定

 値：Reservationクラス、Taskクラスのインスタンス
  それぞれのクラスは、Facility, CalProp, TaskList, TaskProp　をロード

 */

/*

    検索キーワード （ $requestの中の値　）

    base_date : 検索の記事日
    span      : 検索期間
        
    search_reservations : TRUEなら予定を検索する
    search_tasks     : TRUEならタスクを検索する
    
    設備予約・タスクを検索する対象
    facilities   : 予定検索対象設備　　（検索フォームでチェックしたモノ、指定がなければ、ユーザがwritableの設備の予定を検索）
    
    設備・タスクリストの検索条件
    facility_permission  readable, writable, 

    返値
    
    returns['reservations']
    returns['tasks']
    returns['facilities'] 　検索フォームに表示する設備のコレクション 
    未実装　returns['user_role_of_facilities'] : 各設備　へのユーザアクセス権限（キー facility_id, 値 owner, reader, writer
    未実装　returns['user_role_of_facilities'] : 各タスクリストへのユーザアクセス権限（キー tasllist_id, 値 owner, reader, writer
    
    //　下記は週次表示・日次表示で部署毎・社員毎に予定表示するためのデータ
    //
    returns['users']  予定・タスク作成者のリスト（キー　user_id, 値 Userのインスタンス）
    returns['depts'] 予定・タスク作成者の部署リスト（キー dept_id 値 Deptのインスタンス）
    returns['dept_user'] 作成者と所属部署の配列（　１次キー　dept_id 値　所属社員のuser_idの配列）
*/

class CheckAvailableFacilities {
    
    public static function search( Request $request ) {
        
        //　検索対象期間を決定
        //
        $dates = self::calc_date( $request->base_date, $request->span, $request->start_time, $request->end_time );
        
        //　検索条件の初期化
        //
        $start_date = Arr::first( $dates )->format( 'Y-m-d' );
        $end_date   = Arr::last(  $dates )->format( 'Y-m-d' );

        //　検査フォームに表示する設備を検索
        //
        $facilities = self::searchFacilities( $request );
        
        if_debug( __METHOD__, $facilities );
        //　設備予約の検索
        //
        $reservations = self::searchReservations( $request, $dates );
        
        //　設備表示用データの作成
        //
        if( $request->span == "weekly" ) {
            $returns = self::arrange_outputs_for_weekly( $dates, $reservations, $facilities  );   
        } else {
            $returns = self::arrange_outputs( $dates, $reservations, $facilities );
        }

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
        
        //　設備予約の検索
        //
        $reservations = Reservation::with( [ 'facility', 'user', 'user.dept' ] );
        $reservations = $reservations->orderBy( 'start' );

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
        $reservations->whereHas( 'facility', function( $query ) use ( $facilities ) {
            $query->whereIn( 'id', $facilities );
        });

        $reservations = $reservations->get();

        return $reservations;
    }
    
    //　設備を検索
    //
    public static function searchFacilities( Request $request ) {
        if( ! is_array( $request->facilities ) or ! count( $request->facilities )) { return []; }
        $facilities = Facility::find( $request->facilities );
        if_debug( __METHOD__, $facilities );
        return $facilities;
    }
    
    //　検索対象の期間（日付の配列を取得）
    //　月表示設備が四角になるように
    //　週初めを日曜日に統一させるため
    //
    //　返値
    //　$dates['Y-m-d']['H:i'] = Carbonインスタンス

    private static function calc_date( $base_date, $span, $start_time, $end_time ) {
        
        $base_date = ( $base_date instanceof Carbon ) ? $base_date : new Carbon( $base_date );
        
        //　検索対象の期間を計算
        //
        if( $span == 'monthly' ) {
            $first_date = $base_date->copy()->firstOfMonth();
            $end_date   = $base_date->copy()->endOfMonth();

            //  週初めが日曜日
            //　週の終わりが土曜日
            //
            while( ! $first_date->isSunday() ) { $first_date->subDay(); }
            while( ! $end_date->isSaturday() ) { $end_date->addDay();  }

        } elseif( $span == 'weekly' ) {
            $first_date = $base_date->copy();
            $end_date   = $base_date->copy()->addDays(6);

        } else { // １日のみ検索 'daily'
            $first_date = $base_date->copy();
            $end_date   = $base_date->copy();
        }
        
        //　月・週・日　表示設備のデータ作成
        //
        $count = $first_date->diffInDays( $end_date );
        $dates = [];
        
        for ($i = 0; $i <= $count; $i++, $first_date->addDay()) {
            // copyしないと全部同じオブジェクトを入れてしまうことになる
            $dates[$first_date->format( 'Y-m-d' )] = $first_date->copy();
        }

        if_debug( __METHOD__,  $dates );
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

    //　下記はマンスリー表示用データ
    //
    //  $returns['multi']　２日間以上の予定の配列
    //  $returns['single'] １日終日の予定
    //  $returns['time']  １日以内の予定
    //  $returns['others'] その他　何件
    //  $returns['others']['Y-m-d'] 値はその他の件数（７件目以上はその他表示）
    //
    //  下記はデイリー表示用のデータ
    //  $returns['all_day']　終日の予定フラグがついている予定
    //  $returns['multi_not_all_day'] 複数日の予定で終日フラグがついてない予定
    //  $returns['time_for_daily'][時刻] 　終日フラグでない予定（１日以内、複数日含む）
    // 
    private static function arrange_outputs( $dates, $reservations,  $facilities ) {

        //　設備表示用データの作成
        //
        $start_date = Arr::first( $dates )->format( 'Y-m-d' );
        $end_date   = Arr::last(  $dates )->format( 'Y-m-d' );
        $returns = [ 'dates' => $dates, 
                     'multi' => [], 
                     'single' => [], 
                     'task' => [], 
                     'time' => [], 
                     'facilities' => [], 
                     'list_of_tasklists' => [],
                     
                     'all_day' => [],
                     'time_for_daily' => [],
                     'multi_not_all_day' => [],
                     
                     ];
        list( $returns['cols'], $returns['rows'] ) = self::calc_date_col_and_row( $dates );
        
        foreach( $reservations as $reservation ) {
            
            //　予定の日数（１日以内 or 複数日か　)
            //
            $num_day = $reservation->getNumDates();
            $date = $reservation->start->copy();

            if( $num_day >= 2 ) {

                //　月・週表示用データ（予定が複数日の場合の処理）
                //
                array_push( $returns['multi'], $reservation ); 
                
                //　デイリー表示用のデータ
                //
                if( $reservation->all_day ) { 
                    array_push( $returns['all_day'], $reservation );
                } else {
                    $time = $reservation->start->format('H:i');
                    if( ! isset( $returns['time_for_daily'][$time]) ) { $returns['time_for_daily'][$time] = []; }
                    array_push( $returns['time_for_daily'][$time], $reservation );
                    array_push( $returns['multi_not_all_day'], $reservation );
                }
                
            } else {
                //　１日だけの予定の処理
                //
                if( $reservation->all_day ) {
                    array_push( $returns['single' ], $reservation );
                    array_push( $returns['all_day'], $reservation );
                } else {
                    //　月・週表示用データ
                    //
                    array_push( $returns['time'], $reservation );
                    
                    //　デイリー表示用データ
                    //
                    $time = $reservation->start->format('H:i');
                    if( ! isset( $returns['time_for_daily'][$time]) ) { $returns['time_for_daily'][$time] = []; }
                    array_push( $returns['time_for_daily'][$time], $reservation );
                }
            }
        }
        foreach( $facilities as $facility ) {
            array_push( $returns['facilities'], $facility );
        }
        foreach( $list_of_tasklists as $tasklist ) {
            array_push( $returns['list_of_tasklists'], $tasklist );
        }
        
        $returns['task'] = [];
        foreach( $tasks as $task ) {
            array_push( $returns['task'] , $task );
        }
        
        $returns['users'] = [];
        $returns['depts'] = [];
        $returns['dept_user'] = [];
        
        foreach( [ $reservations, $tasks ] as $instances ) {
            foreach( $instances as $instance ) {
                $user = $instance->user;
                $dept = $user->dept;
                $returns['users'][$user->id] = $user;
                $returns['depts'][$dept->id] = $dept;
                
                if( is_array( op( $returns['dept_user'] )[ $dept->id ] )) {
                    if( ! in_array( $user->id, $returns['dept_user'][$dept->id] )) {
                        array_push( $returns['dept_user'][$dept->id], $user->id );
                    }
                } else {
                    $returns['dept_user'][ $dept->id ] = [ $user->id ];
                }
                
            }
        }
        
        //　デイリー表示用に時間でデータをソート
        //
        // if_debug( $returns['time_for_daily'], ksort( $returns['time_for_daily'] ), $returns['time_for_daily'], $returns );
        ksort( $returns['time_for_daily' ] );
        // dd( $returns['time_for_daily']);

        return $returns;
        
    }
    
        //
    //　出力データの整形
    //
    //  $returns['dates'] キーが日付テキスト、値がCarbon
    // 
    //  下記はウイークリー表示
    //
    //  $returns['depts']  キー；dept_id , 値：部署のインスタンス
    //  $returns['users']  キー：user_id , 値：ユーザのインスタンス
    //  $returns[user_id]['num_of_objects'][Y-m-d] ユーザのある日に何個オブジェクトがあるか
    //  $returns[user_id]['max_num_of_objects']      ユーザの最大オブジェクト数
    //
    //  $returns[user_id][Y-m-d]['multi']　２日間以上の予定の配列（表示初日のみインスタンス、２日目以降は１を入力）
    //  $returns[user_id][Y-m-d]['single'] １日終日の予定
    //  $returns[user_id][Y-m-d]['task']   タスク
    //  $returns[user_id][Y-m-d]['time']  １日以内の予定
    //  $returns[user_id][Y-m-d]['others'] その他　何件 ２０件以上はその他
    //
    private static function arrange_outputs_for_weekly( $dates, $reservations,  $facilities  ) {

        //　設備表示用データの作成
        //
        $start_date = Arr::first( $dates )->format( 'Y-m-d' );
        $end_date   = Arr::last(  $dates )->format( 'Y-m-d' );
        $returns['dates'] = $dates;

        //　出力値の初期化
        //
        foreach( $facilities as $facility ) {
            foreach( $dates as $date ) {
                $returns[$facility->id]['num_of_objects'][$date->format('Y-m-d')] = 0;
                $returns[$facility->id][$date->format('Y-m-d')] = [ 'multi' => [], 'single' => [], 'task' => [], 'time' => [], 'others' => [] ];        
            }
            $returns[$facility->id]['max_num_of_objects'] = 0;                
        }

        list( $returns['cols'], $returns['rows'] ) = self::calc_date_col_and_row( $dates );

        if_debug( $returns );

        //　設備予約を週表示用にデータ処理
        // 
        foreach( $reservations as $reservation ) {

            //　予定の日数（１日以内 or 複数日か　)
            //
            $num_day = $reservation->getNumDates();
            $user = $reservation->user;

            if( $num_day >= 2 ) {

                //　予定が複数日の場合
                //
                $start = 0;
                foreach( $dates as $date ) {
                    if( $start == 0 and ( $date->gte( $reservation->start ) or $date->format('Y-m-d') == $reservation->start->format('Y-m-d') )) {
                        $start = 1;
                        // dd($returns[$user->id][$date->format('Y-m-d')]['multi'] );
                        array_push( $returns[$user->id][$date->format('Y-m-d')]['multi'] , $reservation );
                        $returns[$user->id]['num_of_objects'][$date->format('Y-m-d')]++;    
                        
                    } elseif( $start == 1 and ( $date->lte( $reservation->end ) or $date->format('Y-m-d') == $reservation->end->format('Y-m-d') )) {
                        $returns[$user->id]['num_of_objects'][$date->format('Y-m-d')]++;                        
                        // array_push( $returns[$user->id][$date->format('Y-m-d')]['multi'] , $reservation );
                        array_push( $returns[$user->id][$date->format('Y-m-d')]['multi'] , 1 );
                    }
                }
            } else {
                //　１日だけの予定の処理
                //
                if( $reservation->all_day ) {
                    array_push( $returns[$user->id][$reservation->start->format('Y-m-d')]['single'], $reservation );
                } else {
                    array_push( $returns[$user->id][$reservation->start->format('Y-m-d')]['time'], $reservation );
                }
                $returns[$user->id]['num_of_objects'][$reservation->start->format('Y-m-d')]++;
            }
        }

        //　設備別　最大オブジェクト表示数
        //
        foreach( $facilities as $facility ) {
            $nums = $returns[$facility->id]['num_of_objects'];
            $returns[$facility->id]['max_num_of_objects'] = max( $nums );
        }

        //　設備の表示用データ
        //
        $returns['facilities'] = [];
        foreach( $facilities as $facility ) {
            array_push( $returns['facilities'], $facility );
        }
        
        if_debug( __METHOD__, $returns );
        return $returns;
    }
}

