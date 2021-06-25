<?php

namespace App\myHttp\GroupWare\Controllers\Search;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use DB;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\AccessListUserRole;
use App\myHttp\GroupWare\Models\Actions\AccessListAction;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;

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

 値：Scheduleクラス、Taskクラスのインスタンス
  それぞれのクラスは、Calendar, CalProp, TaskList, TaskProp　をロード

 */

/*

    検索キーワード （ $requestの中の値　）

    base_date : 検索の記事日
    span      : 検索期間
        
    search_schedules : TRUEなら予定を検索する
    search_tasks     : TRUEならタスクを検索する
    
    スケジュール・タスクを検索する対象
    calendars   : 予定検索対象カレンダー　　（検索フォームでチェックしたモノ、指定がなければ、ユーザがwritableのカレンダーの予定を検索）
    tasklists   ：タクス検索対象タスクリスト（検索フォームでチェックしたモノ、指定がなければ、ユーザがwritableのタスクリストのタスクを対象）
    
    カレンダー・タスクリストの検索条件
    calendar_permission  readable, writable, 
    tasklist_permission  readable, writable, 
    show_hidden_calendars : 非表示設定したカレンダーを検索対象にする( calprop->hide  )
    show_hidden_tasklists : 非表示設定したカレンダーも検索対象にする( taskprop->hide )

    返値
    
    returns['schedules']
    returns['tasks']
    returns['list_of_calendars'] 　検索フォームに表示するカレンダーのコレクション 
    returns['list_of_tasklists']　検索フォームに表示するタスクリストのコレクション
    未実装　returns['user_role_of_calendars'] : 各カレンダー　へのユーザアクセス権限（キー calendar_id, 値 owner, reader, writer
    未実装　returns['user_role_of_calendars'] : 各タスクリストへのユーザアクセス権限（キー tasllist_id, 値 owner, reader, writer
    
    //　下記は週次表示・日次表示で部署毎・社員毎に予定表示するためのデータ
    //
    returns['users']  予定・タスク作成者のリスト（キー　user_id, 値 Userのインスタンス）
    returns['depts'] 予定・タスク作成者の部署リスト（キー dept_id 値 Deptのインスタンス）
    returns['dept_user'] 作成者と所属部署の配列（　１次キー　dept_id 値　所属社員のuser_idの配列）
*/

class SearchSchedulesAndTasks {

    
    public static function search( Request $request ) {
        
        //　検索対象期間を決定
        //
        $dates = self::calc_date( $request->base_date, $request->span );
        
        //　検索条件の初期化
        //
        $start_date = Arr::first( $dates )->format( 'Y-m-d' );
        $end_date   = Arr::last(  $dates )->format( 'Y-m-d' );

        //　検査フォームに表示するカレンダーを検索
        //
        $list_of_calendars = self::searchCalendars( $request );
        $list_of_tasklists = self::searchTaskLists( $request );
        
        //　スケジュールの検索
        //
        $schedules = self::searchSchedules( $request, $dates );
        
        //　タスクを検索
        //
        $tasks = self::searchTasks( $request, $dates );



        //　カレンダー表示用データの作成
        //
        
        $returns = self::arrange_outputs( $dates, $schedules, $tasks, $list_of_calendars, $list_of_tasklists );

        return $returns;
    }

    //　スケジュールを検索
    //
    private static function searchSchedules( Request $request, $dates ) {

        if( ! is_array( $request->calendars )) { return []; }

        //　検索条件の初期化
        //
        $start_date = Arr::first( $dates )->format( 'Y-m-d' );
        $end_date   = Arr::last(  $dates )->format( 'Y-m-d' );
        
        $calendars  = ( is_array( $request->calendars )) ? $request->calendars : [];
        
        //　スケジュールの検索
        //
        $schedules = Schedule::with( [ 'calendar', 'calendar.calprop', 'user', 'user.dept' ] );
        $schedules = $schedules->orderBy( 'start' );

        $schedules = $schedules->where( function( $sub_query ) use ( $start_date, $end_date ) {
            $sub_query->where( function( $query ) use ( $start_date, $end_date ) {
                        $query->where( 'start_date', '>=', $start_date )
                              ->where( 'start_date', '<=', $end_date   );
                        });
            $sub_query->orWhere( function( $query ) use( $start_date, $end_date ) {
                        $query->where( 'end_date', '>=', $start_date )
                              ->where( 'end_date', '<=', $end_date   );
                        });
            $sub_query->orWhere( function( $query ) use( $start_date, $end_date ) {
                        $query->where( 'start_date', '<', $start_date )
                              ->where( 'end_date',   '>', $end_date   );
                        });
        });

        // if( ! count( $calendars )) {
        //     $calendars = ( $calendar_permission == "writable" ) ? Calendar::getCanWrite( user_id() ) : Calendar::getCanRead( user_id() );
        //     $calendars_ids = $calendars->pluck( 'id' );
            
        //     $calendars = Calendar::whereHas( 'calprops', function( $query ) use ( $show_hidden_calendars ) {
        //         $query->where( 'user_id', user_id() );
        //         if( ! $show_hidden_calendars ) {
        //             $query->where( 'hide', 0 );
        //         }
        //     });
        //     $calendars = $calendars->whereIn( 'id', $calendars_ids )->get()->pluck( 'id' );
        // }

        $schedules->whereHas( 'calendar', function( $query ) use ( $calendars ) {
            $query->whereIn( 'id', $calendars );
        });

        $schedules = $schedules->get();

        return $schedules;
    }
    
    //　タスクを検索
    //
    private static function searchTasks( Request $request, $dates ) {
        
        if( ! is_array( $request->tasklists )) { return []; }
        
        //　検索条件の初期化
        //
        $start_date = Arr::first( $dates )->format( 'Y-m-d' );
        $end_date   = Arr::last(  $dates )->format( 'Y-m-d' );
        
        $tasklists  = $request->tasklists;

        // $tasklist_permission = ( ! empty( $request->tasklist_permission )) ? $request->tasklist_permission : 'writable';
        // $show_hidden_tasklists = ( $request->show_hidden_tasklists ) ? 1 : 0 ;

        $tasks = Task::with( [ 'tasklist', 'tasklist.taskprop', 'user', 'user.dept' ] );
        $tasks = $tasks->orderBy( 'due_time' );

        $tasks = $tasks->where( 'due_date', '>=', $start_date )
                       ->where( 'due_date', '<=', $end_date   );
        
        if( isset( $request->task_status )) {
            $tasks = $tasks->where( 'status', $request->task_status );
        }        

        $tasks->whereHas( 'tasklist', function( $query ) use ( $tasklists ) {
            $query->whereIn( 'id', $tasklists );
        });

        $tasks = $tasks->get();

        return $tasks;
    } 

    //　カレンダーを検索
    //
    public static function searchCalendars( Request $request ) {
        
        if( $request->calendar_permission == "owner" ) {
            $calendars = Calendar::getOwner( user_id() );
        } elseif( $request->calendar_permission == "reader" ) {
            $calendars = Calendar::getCanRead( user_id() );
        } else {
            $calendars = Calendar::getCanWrite( user_id() );
        }

        $calendars = Calendar::with( ['calprop' ] )->whereIn( 'id', $calendars->pluck('id')->toArray() );
        
        if( ! $request->show_hidden_calendars ) {
            $calendars = $calendars->whereHas( 'calprop', function( $query ) { $query->where( 'hide', 0 );  });
        }
        $calendars = $calendars->get();
        
        return $calendars;
    }
    
    //　タクスリストを検索
    //
    public static function searchTaskLists( Request $request ) {
        
        if( $request->tasklist_permission == "owner" ) {
            $tasklists = TaskList::getOwner( user_id() );
        } elseif( $request->tasklist_permission == "reader" ) {
            $tasklists = TaskList::getCanRead( user_id() );
        } else {
            $tasklists = TaskList::getCanWrite( user_id() );
        }

        $tasklists = TaskList::with( ['taskprop' ] )->whereIn( 'id', $tasklists->pluck('id')->toArray() );
        
        if( ! $request->show_hidden_tasklists ) {
            $tasklists = $tasklists->whereHas( 'taskprop', function( $query ) { $query->where( 'hide', 0 );  });
        }
        $tasklists = $tasklists->get();
        
        return $tasklists;
    }

    //　検索対象の期間（日付の配列を取得）
    //　月表示カレンダーが四角になるように
    //　週初めを日曜日に統一させるため
    //
    private static function calc_date( $base_date, $span ) {
        
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
            $end_date   = $base_date->copy();

            //  週初めが日曜日
            //　週の終わりが土曜日
            //
            while( ! $first_date->isSunday() ) { $first_date->subDay(); }
            while( ! $end_date->isSaturday() ) { $end_date->addDay();  }

        } else { // １日のみ検索 'daily'
            $first_date = $base_date->copy();
            $end_date   = $base_date->copy();
        }
        
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

    //
    //　出力データの整形
    //
    //  $returns['dates'] キーが日付テキスト、値がCarbon
    //  $returns['multi']　２日間以上の予定の配列
    //  $returns['single'] １日終日の予定
    //  $returns['task']   タスク
    //  $returns['time']  １日以内の予定
    //  $returns['others'] その他　何件
    //  $returns['others']['Y-m-d'] 値はその他の件数（７件目以上はその他表示）
    //
    private static function arrange_outputs( $dates, $schedules, $tasks, $list_of_calendars, $list_of_tasklists ) {

        //　カレンダー表示用データの作成
        //
        $start_date = Arr::first( $dates )->format( 'Y-m-d' );
        $end_date   = Arr::last(  $dates )->format( 'Y-m-d' );
        $returns = [ 'dates' => $dates, 'multi' => [], 'single' => [], 'task' => [], 'time' => [], 'list_of_calendars' => [], 'list_of_tasklists' => [] ];
        list( $returns['cols'], $returns['rows'] ) = self::calc_date_col_and_row( $dates );
        
        foreach( $schedules as $schedule ) {
            
            //　予定の日数（１日以内 or 複数日か　)
            //
            $num_day = $schedule->end->diffInDays( $schedule->start ) + 1;
            // if_debug( $schedule->name, $num_day );
            $date = $schedule->start->copy();

            if( $num_day >= 2 ) {

                //　予定が複数日の場合の処理
                //
                array_push( $returns['multi'], $schedule ); 
            } else {
                //　１日だけの予定の処理
                //
                if( $schedule->all_day ) {
                    array_push( $returns['single'], $schedule );
                } else {
                    array_push( $returns['time'], $schedule );
                }
            }
        }
        foreach( $list_of_calendars as $calendar ) {
            array_push( $returns['list_of_calendars'], $calendar );
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
        
        foreach( [ $schedules, $tasks ] as $instances ) {
            foreach( $instances as $instance ) {
                $user = $instance->user;
                $dept = $user->dept;
                $returns['users'][$user->id] = $user;
                $returns['depts'][$dept->id] = $dept;
                
                if( is_array( op( $returns['dept_user'] )[ $dept->id ] )) {
                    if( ! in_array( $user->id, $returns['dept_user'][$dept->id] )) {
                        array_push( op( $returns['dept_user'] )[$dept->id], $user->id );
                    }
                } else {
                    $returns['dept_user'][ $dept->id ] = [ $user->id ];
                }
                
            }
        }
        return $returns;
        
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

}

