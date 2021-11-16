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
        $current_route_name = Route::currentRouteName();
        #dd( $current_route_name );
        if( $current_route_name == "groupware.show_all.weekly" ) {
            $returns = self::arrange_outputs_for_weekly( $dates, $schedules, $tasks, $list_of_calendars, $list_of_tasklists, $request  );  
            
        } elseif( $current_route_name == "groupware.show_all.daily" or 
                  $current_route_name == "groupware.show_all.dialog.daily" ) {
            // $returns = self::arrange_outputs_for_weekly( $dates, $schedules, $tasks, $list_of_calendars, $list_of_tasklists, $request  );   

            // $returns = self::arrange_outputs( $dates, $schedules, $tasks, $list_of_calendars, $list_of_tasklists );            
            $returns = self::arrange_outputs_for_daily( $dates, $schedules, $tasks, $list_of_calendars, $list_of_tasklists, $request  );   
        } else {
            $returns = self::arrange_outputs( $dates, $schedules, $tasks, $list_of_calendars, $list_of_tasklists );
        }

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
        
        //　開始が早く、終了日が遅い順でソート
        //
        // $schedules = $schedules->orderBy( 'start', 'asc' )->orderBy( 'end_date', 'desc' );
        $schedules = $schedules->orderByRaw( 'start asc, end desc' );

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

        //　部署・社員検索
        //
        if( $request->users or $request->depts ) {

            if( $request->users and $request->depts ) {
                $query_for_user = User::whereIn( 'id', $request->users )
                                      ->orWhere( function( $query ) use ( $request ) {
                                                    $query->whereIn( 'dept_id', $request->depts );
                                               });
            } elseif( ! $request->users and $request->depts ) {
                $query_for_user = User::whereIn( 'dept_id', $request->depts );
    
            } elseif( $request->users and ! $request->depts ) {
                $query_for_user = User::whereIn( 'id', $request->users );
            }
            $user_ids = $query_for_user->get()->pluck( 'id' )->toArray();
            
            if( ! $request->search_users ) {
    
                // 　予定作成者のみ検索
                //
                $schedules = $schedules->whereIn( 'user_id', $user_ids );
                $schedules = $schedules->with( [ 'users' => function( $query ) { $query->where( 'id', -1 ); } ]);

            } else {
    
                // 　予定作成者と関連社員で検索
                //
                $schedules = $schedules->where( function( $query ) use( $user_ids ) {
                                                        $query->whereIn( 'user_id', $user_ids )
                                                              ->orWhere( function( $query2 ) use ( $user_ids ) {
                                                                    $query2->whereHas( 'users', function( $query3 ) use( $user_ids )  {
                                                                                $query3->whereIn( 'id', $user_ids );
                                                                            });
                                                                    });
                                                        });
                $schedules = $schedules->with( [ 'users' => function( $query ) use( $user_ids ) { $query->whereIn( 'id', $user_ids ); }] );
            }
        } else {
            $schedules = $schedules->with( [ 'users' => function( $query ) { $query->where( 'id', -1 ); } ]);
        }

        //　関連顧客で検索
        //
        if( $request->customers ) {
            $schedules = $schedules->whereHas( 'customers', function( $query ) use ( $request ) {
                                        $query->whereIn( 'id', $request->customers ); 
                                    });
        }
        $schedules->whereHas( 'calendar', function( $query ) use ( $calendars ) {
            $query->whereIn( 'id', $calendars );
        });
        
        // if_debug( $schedules );
        $schedules = $schedules->get();
        // if_debug( $schedules );

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
        
        //　ステータス検索
        //
        if( isset( $request->task_status )) {
            $tasks = $tasks->where( 'status', $request->task_status );
        }
        
        //　部署・社員検索
        //
        if( $request->users or $request->depts ) {
            if( $request->users and $request->depts ) {
                $query_for_user = User::whereIn( 'id', $request->users )
                                      ->orWhere( function( $query ) use ( $request ) {
                                                    $query->whereIn( 'dept_id', $request->depts );
                                               });
            } elseif( ! $request->users and $request->depts ) {
                $query_for_user = User::whereIn( 'dept_id', $request->depts );
    
            } elseif( $request->users and ! $request->depts ) {
                $query_for_user = User::whereIn( 'id', $request->users );
            }
            $user_ids = $query_for_user->get()->pluck( 'id' )->toArray();
            
            if( ! $request->search_users ) {
    
                // 　タスク作成者のみ検索
                //
                $tasks = $tasks->whereIn( 'user_id', $user_ids );
                $tasks = $tasks->with( [ 'users' => function( $query ) { $query->where( 'id', -1 ); } ]);
    
            } else {
    
                // 　タスク作成者と関連社員で検索
                //
                $tasks = $tasks->where( function( $query ) use( $user_ids ) {
                                                        $query->whereIn( 'user_id', $user_ids )
                                                              ->orWhere( function( $query2 ) use ( $user_ids ) {
                                                                    $query2->whereHas( 'users', function( $query3 ) use( $user_ids )  {
                                                                                $query3->whereIn( 'id', $user_ids );
                                                                            });
                                                                    });
                                                        });
                $tasks = $tasks->with( [ 'users' => function( $query ) use( $user_ids ) { $query->whereIn( 'id', $user_ids ); }] );
            }
        } else {
            $tasks = $tasks->with( [ 'users' => function( $query ) { $query->where( 'id', -1 ); } ]);
        }

        //　関連顧客で検索
        //
        if( $request->customers ) {
            $tasks = $tasks->whereHas( 'customers', function( $query ) use ( $request ) {
                                        $query->whereIn( 'id', $request->customers ); 
                                    });
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

        //　スケジュール検索対象のカレンダーも検索する
        //
        if( ! empty( $request->calendars )) {
            $calendars = $calendars->orWhere( function( $query ) use ( $request ) {
                $query->whereIn( 'id', $request->calendars );
            });
        }

        if( ! $request->show_hidden_calendars ) {
            $calendars = $calendars->whereHas( 'calprop', function( $query ) { $query->where( 'hide', 0 );  });
        }
        
        //　Disableの検索
        //
        if( ! $request->show_disabled_calendars ) { 
            $calendars->where( 'disabled', 0 ); 
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
            $tasklists->whereHas( 'taskprop', function( $query ) { $query->where( 'hide', 0 );  });
        }
        
        //　Disableの検索
        //
        if( ! $request->show_disabled_tasklists ) { 
            $tasklists->where( 'disabled', 0 ); 
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
    //  $returns['task']   タスク
    //  $returns['time']  １日以内の予定
    //  $returns['others'] その他　何件
    //  $returns['others']['Y-m-d'] 値はその他の件数（７件目以上はその他表示）
    //
    //  下記はデイリー表示用のデータ
    //  $returns['all_day']　終日の予定フラグがついている予定
    //  $returns['multi_not_all_day'] 複数日の予定で終日フラグがついてない予定
    //  $returns['time_for_daily'][時刻] 　終日フラグでない予定（１日以内、複数日含む）
    // 
    private static function arrange_outputs( $dates, $schedules, $tasks, $list_of_calendars, $list_of_tasklists ) {

        //　カレンダー表示用データの作成
        //
        $start_date = Arr::first( $dates )->format( 'Y-m-d' );
        $end_date   = Arr::last(  $dates )->format( 'Y-m-d' );
        $returns = [ 'dates' => $dates, 
                     'multi' => [], 
                     'single' => [], 
                     'task' => [], 
                     'time' => [], 
                     'list_of_calendars' => [], 
                     'list_of_tasklists' => [],
                     
                     'all_day' => [],
                     'time_for_daily' => [],
                     'multi_not_all_day' => [],
                     
                     ];
        list( $returns['cols'], $returns['rows'] ) = self::calc_date_col_and_row( $dates );
        
        foreach( $schedules as $schedule ) {
            
            //　予定の日数（１日以内 or 複数日か　)
            //
            $num_day = $schedule->getNumDates();
            $date = $schedule->start->copy();

            if( $num_day >= 2 ) {

                //　月・週表示用データ（予定が複数日の場合の処理）
                //
                array_push( $returns['multi'], $schedule ); 
                
                //　デイリー表示用のデータ
                //
                if( $schedule->all_day ) { 
                    array_push( $returns['all_day'], $schedule );
                } else {
                    $time = $schedule->start->format('H:i');
                    if( ! isset( $returns['time_for_daily'][$time]) ) { $returns['time_for_daily'][$time] = []; }
                    array_push( $returns['time_for_daily'][$time], $schedule );
                    array_push( $returns['multi_not_all_day'], $schedule );
                }
                
            } else {
                //　１日だけの予定の処理
                //
                if( $schedule->all_day ) {
                    array_push( $returns['single' ], $schedule );
                    array_push( $returns['all_day'], $schedule );
                } else {
                    //　月・週表示用データ
                    //
                    array_push( $returns['time'], $schedule );
                    
                    //　デイリー表示用データ
                    //
                    $time = $schedule->start->format('H:i');
                    if( ! isset( $returns['time_for_daily'][$time]) ) { $returns['time_for_daily'][$time] = []; }
                    array_push( $returns['time_for_daily'][$time], $schedule );
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
    private static function arrange_outputs_for_weekly( $dates, $schedules, $tasks, $list_of_calendars, $list_of_tasklists, $request  ) {

        //　カレンダー表示用データの作成
        //
        $start_date = Arr::first( $dates )->format( 'Y-m-d' );
        $end_date   = Arr::last(  $dates )->format( 'Y-m-d' );
        $returns['dates'] = $dates;

        //　ユーザ・部署　配列
        //
        $users = [];
        $depts = [];

        if( $request->users or $request->depts ) {
    
            if( $request->users and $request->depts ) {
                $Users = User::whereIn( 'id', $request->users )
                                      ->orWhere( function( $query ) use ( $request ) {
                                                    $query->whereIn( 'dept_id', $request->depts );
                                               });
            } elseif( ! $request->users and $request->depts ) {
                $Users = User::whereIn( 'dept_id', $request->depts );
    
            } elseif( $request->users and ! $request->depts ) {
                $Users = User::whereIn( 'id', $request->users );
            }
            $Users = $Users->with( ['dept'] )->get();

            foreach( $Users as $user ) {
                $users[$user->id] = $user;
                $depts[$user->dept->id] = $user->dept;
            }

        } else {
            foreach( [ $schedules, $tasks ] as $objects ) {
                foreach( $objects as $object ) {
                    $user = $object->user;
                    $users[$user->id] = $user;
                    $depts[$user->dept->id] = $user->dept;
                }
            }
        }
        $returns['users'] = $users;
        $returns['depts'] = $depts;
        

        //　出力値の初期化
        //
        foreach( $users as $user ) {
            foreach( $dates as $date ) {
                $returns[$user->id]['num_of_objects'][$date->format('Y-m-d')] = 0;
                $returns[$user->id][$date->format('Y-m-d')] = [ 'multi' => [], 'single' => [], 'task' => [], 'time' => [], 'others' => [] ];        
            }
            $returns[$user->id]['max_num_of_objects'] = 0;                
        }
        
        //　出力値の初期化（関連社員検索関連で追加した処理、「関連社員も検索」のチェックがない場合にエラーになる対応）
        //
        if( $request->search_users ) {
            foreach( [ $schedules, $tasks ] as $objects ) {
                foreach( $objects as $object ) {
                    $user = $object->user;
                    if( array_key_exists( $user->id, $users )) { continue; }
                    
                    foreach( $dates as $date ) {
                        $returns[$user->id]['num_of_objects'][$date->format('Y-m-d')] = 0;
                        $returns[$user->id][$date->format('Y-m-d')] = [ 'multi' => [], 'single' => [], 'task' => [], 'time' => [], 'others' => [] ];        
                    }
                    $returns[$user->id]['max_num_of_objects'] = 0;                
                }
            }
        }

        list( $returns['cols'], $returns['rows'] ) = self::calc_date_col_and_row( $dates );

        // if_debug( $users, $depts, $returns );

        //　スケジュールを週表示用にデータ処理
        // 
        foreach( $schedules as $schedule ) {

            //　予定の日数（１日以内 or 複数日か　)
            //
            $num_day = $schedule->getNumDates();

            //　関連社員検索関係で追加した処理
            //
            $users_in_schedule = [];
            if( $request->search_users and count( $schedule->users ) >= 1 ) {
                $tmp_users = Arr::collapse( [ [$schedule->user], $schedule->users ] );
                foreach( $tmp_users as $user ) {
                    $users_in_schedule[$user->id] = $user;
                }
            } else {
                $user = $schedule->user;
                $users_in_schedule[$user->id] = $user;
            }
            
            foreach( $users_in_schedule as $user ) {
                // $user = $schedule->user;

                if( $num_day >= 2 ) {
    
                    //　予定が複数日の場合
                    //
                    $start = 0;
                    foreach( $dates as $date ) {
                        if( $start == 0 and ( $date->gte( $schedule->start ) or $date->format('Y-m-d') == $schedule->start->format('Y-m-d') )) {
                            $start = 1;
                            // dd($returns[$user->id][$date->format('Y-m-d')]['multi'] );
                            array_push( $returns[$user->id][$date->format('Y-m-d')]['multi'] , $schedule );
                            $returns[$user->id]['num_of_objects'][$date->format('Y-m-d')]++;  
                            $order = $returns[$user->id]['num_of_objects'][$date->format('Y-m-d')];  
                            
                        } elseif( $start == 1 and ( $date->lte( $schedule->end ) or $date->format('Y-m-d') == $schedule->end->format('Y-m-d') )) {
                            // $returns[$user->id]['num_of_objects'][$date->format('Y-m-d')]++;                        
                            $returns[$user->id]['num_of_objects'][$date->format('Y-m-d')] = $order;                        
                            // array_push( $returns[$user->id][$date->format('Y-m-d')]['multi'] , $schedule );
                            array_push( $returns[$user->id][$date->format('Y-m-d')]['multi'] , 1 );
                        }

                        if( 0 and $user->id == 1 ) {
                            if_debug( __METHOD__, $date->format('Y-m-d'), $schedule->name, $returns[$user->id]['num_of_objects'] );
                        }


                    }
                } else {
                    //　１日だけの予定の処理
                    //
                    if( $schedule->all_day ) {
                        array_push( $returns[$user->id][$schedule->start->format('Y-m-d')]['single'], $schedule );
                    } else {
                        array_push( $returns[$user->id][$schedule->start->format('Y-m-d')]['time'], $schedule );
                    }
                    $returns[$user->id]['num_of_objects'][$schedule->start->format('Y-m-d')]++;
                }

            }
        }
        

        //　タスクを週表示用にデータ処理
        //
        foreach( $tasks as $task ) {
            
            $users_in_task = [];
            if( $request->search_users and count( $task->users ) >= 1 ) {
                foreach( Arr::collapse( [[ $task->user ], $task->users ] ) as $user ) {
                    $users_in_task[$user->id] = $user;
                }
            } else {
                $users_in_task[$task->user->id] = $task->user;
            }

            foreach( $users_in_task as $user ) {            
                // $user = $task->user;
                $date = $task->due_time->format( 'Y-m-d' );
            
                array_push( $returns[$user->id][$date]['task'], $task );
                $returns[$user->id]['num_of_objects'][$date]++;
            }
        }
        
        //　ユーザ別　最大オブジェクト表示数
        //
        foreach( $users as $user ) {
            $nums = $returns[$user->id]['num_of_objects'];
            $returns[$user->id]['max_num_of_objects'] = max( $nums );
        }

        //　カレンダー・タスクリストの表示用データ
        //
        $returns['list_of_calendars'] = [];
        $returns['list_of_tasklists'] = [];
        foreach( $list_of_calendars as $calendar ) {
            array_push( $returns['list_of_calendars'], $calendar );
        }
        foreach( $list_of_tasklists as $tasklist ) {
            array_push( $returns['list_of_tasklists'], $tasklist );
        }
        
        
        #if_debug( $returns );
        return $returns;
    }
    
    
    //
    //　出力データの整形
    //
    //  $returns['dates'] キーが日付テキスト、値がCarbon

    //　下記はマンスリー表示用データ
    //
    //  $returns['multi']　２日間以上の予定の配列
    //  $returns['single'] １日終日の予定
    //  $returns['task']   タスク
    //  $returns['time']  １日以内の予定
    //
    //  $returns['users']　キー User ID 値　Userのインスタンス
    //  $returns['depts']　キー Dept ID 値 Deptのインスタンス
    //  $returns['dept_user'] キー Dept ID 値　部署所属のuser_id の配列
    // 
    private static function arrange_outputs_for_daily( $dates, $schedules, $tasks, $list_of_calendars, $list_of_tasklists, $request ) {

        //　カレンダー表示用データの作成
        //
        $start_date = Arr::first( $dates )->format( 'Y-m-d' );
        $end_date   = Arr::last(  $dates )->format( 'Y-m-d' );
        $returns = [ 'dates' => $dates, 
                     'multi' => [], 
                     'single' => [], 
                     'task' => [], 
                     'time' => [], 
                     'list_of_calendars' => [], 
                     'list_of_tasklists' => [],

                     ];
        list( $returns['cols'], $returns['rows'] ) = self::calc_date_col_and_row( $dates );
        
        //　ユーザ・部署　配列
        //
        $users = [];
        $depts = [];

        if( $request->users or $request->depts ) {
    
            if( $request->users and $request->depts ) {
                $Users = User::whereIn( 'id', $request->users )
                                      ->orWhere( function( $query ) use ( $request ) {
                                                    $query->whereIn( 'dept_id', $request->depts );
                                               });
            } elseif( ! $request->users and $request->depts ) {
                $Users = User::whereIn( 'dept_id', $request->depts );
    
            } elseif( $request->users and ! $request->depts ) {
                $Users = User::whereIn( 'id', $request->users );
            }
            $Users = $Users->with( ['dept'] )->get();

            foreach( $Users as $user ) {
                $users[$user->id] = $user;
                $depts[$user->dept->id] = $user->dept;
            }

        }
        foreach( [ $schedules, $tasks ] as $objects ) {
            foreach( $objects as $object ) {
                $user = $object->user;
                $users[$user->id] = $user;
                $depts[$user->dept->id] = $user->dept;
            }
        }
        $returns['users'] = $users;
        $returns['depts'] = $depts;

        $returns['dept_user'] = [];
        foreach( $users as $user ) {
                $returns['dept_user'][ $user->dept->id ][$user->id] = $user;
        }
        
        //　出力用データの初期化
        //
        foreach( $users as $user ) {
            $returns[$user->id] = [ 'multi' => [], 'single' => [], 'task' => [], 'time' => [] ]; 
        }

        //　ユーザ別に予定・タスクを振り分け
        //
        foreach( Arr::collapse( [ $schedules, $tasks ] ) as $object ) {
            
            $users_in_object = [];
                
            if( $request->search_users and count( $object->users ) >= 1 ) {
                $tmp_users = Arr::collapse( [ [$object->user], $object->users ] );
                foreach( $tmp_users as $user ) {
                    $users_in_object[$user->id] = $user;
                }
            } else {
                $users_in_object[$object->user->id] = $object->user;
            }

            foreach( $users_in_object as $user ) {
                if( $object instanceof Schedule ) {
                    if( $object->getNumDates() >= 2 ) {
                        array_push( $returns[$user->id]['multi'], $object ); 
                        
                    } else {
                        //　１日だけの予定の処理
                        //
                        if( $object->all_day ) {
                            array_push( $returns[$user->id]['single' ], $object );
                        } else {
                            array_push( $returns[$user->id]['time'], $object );
                            
                        }
                    }
                } elseif( $object instanceof Task ) {
                    array_push( $returns[$user->id]['task'] , $object );
                }
            }
            
        }

        foreach( $list_of_calendars as $calendar ) {
            array_push( $returns['list_of_calendars'], $calendar );
        }
        foreach( $list_of_tasklists as $tasklist ) {
            array_push( $returns['list_of_tasklists'], $tasklist );
        }
        
        //　デイリー表示用に時間でデータをソート
        //
        // if_debug( $returns['time_for_daily'], ksort( $returns['time_for_daily'] ), $returns['time_for_daily'], $returns );
        // ksort( $returns['time_for_daily' ] );
        // dd( $returns['time_for_daily']);

        #if_debug( $returns );
        // dd( $returns );

        return $returns;
        
    }
    
}

