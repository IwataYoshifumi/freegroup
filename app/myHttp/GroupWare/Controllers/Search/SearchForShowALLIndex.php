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
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;

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

    // 下記２点はどちらかが必須
    start_date, end_date : 検索期間
    keyword : キーワード
        
    スケジュール・タスクを検索する対象
    calendars   : 予定検索対象カレンダー　　（検索フォームでチェックしたモノ、指定がなければ、ユーザがwritableのカレンダーの予定を検索）
    tasklists   ：タクス検索対象タスクリスト（検索フォームでチェックしたモノ、指定がなければ、ユーザがwritableのタスクリストのタスクを対象）

    users
    depts
    customers
    
    カレンダー・タスクリストの検索条件
    show_hidden_calendars : 非表示設定したカレンダーを検索対象にする( calprop->hide  )
    show_hidden_tasklists : 非表示設定したカレンダーも検索対象にする( taskprop->hide )

    task_status : 未完・完了・未完と完了

    返値（一覧表示フォーム用出力データ）
    
    returns['data']      ペジネーションデータ（　インスタンスタイプ( Schedule, Task ), id
    returns['schedules'] スケジュールのインスタンスのコレクション
    returns['tasks']     タスクのインスタンスのコレクション
*/

class SearchForShowALLIndex {

    
    public static function search( Request $request ) {
        
        //　検索条件のバリデーション
        //
        $returns = [];

        //　スケジュールの検索
        //
        $schedules = self::getSchedules( $request );
        
        //　日報の検索
        //
        $reports = self::getReports( $request );
        
        //　タスクを検索
        //
        $tasks = self::getTasks( $request );
        
        //　カレンダー表示用データの作成
        //
        // dump( $schedules, $tasks, 'search' );
        $returns = self::arrange_outputs( $request, $schedules, $tasks, $reports );

        return $returns;
    }
    

    //　スケジュールを検索
    //
    private static function getSchedules( Request $request ) {

        if( ! is_array( $request->calendars )) { return collect( [] ); }


        //　検索対象カレンダー
        //
        // $schedules = new Schedule;
        $schedules = Schedule::whereIn( 'calendar_id', $request->calendars )
                             ->with( [ 'user', 'user.dept', 'calendar.calprop' ]);
    
        
        //　キーワード検索（件名 or 備考）
        //
        if( $request->keyword ) {
            $keyword = '%' . addcslashes( $request->keyword, '%_\\') . '%';
            $schedules = $schedules->where( function( $query ) use( $keyword ) {
                                                    $query->where(   'name', 'like', $keyword )
                                                          ->orWhere( 'memo', 'like', $keyword );
                        });
        }

        //　期間の検索
        //
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        if( $start_date and $end_date ) {        
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
        } elseif( ! $start_date and $end_date ) {
            $schedules = $schedules->where( 'start_date', '<=', $end_date );
        } elseif( $start_date and ! $end_date ) {
            $schedules = $schedules->where( 'end_date', '>=', $start_date );
        }
        
        //　部署・社員検索
        //
        // if( $request->users and $request->depts ) {
        //     $sub_query = User::select('id')->whereIn( 'id', $request->users )
        //                                   ->orWhere( function( $query ) use ( $request ) {
        //                                         $query->whereIn( 'dept_id', $request->depts );
        //                                   });
        //     $schedules = $schedules->whereIn( 'user_id', $sub_query );
        // } elseif( ! $request->users and $request->depts ) {
        //     $schedules = $schedules->whereHas( 'user', function( $query ) use ( $request ) {
        //                                 $query->whereIn( 'dept_id', $request->depts );
        //                     });
        // } elseif( $request->users and ! $request->depts ) {
        //     $schedules = $schedules->whereIn( 'user_id', $request->users );
        // }

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
        //　ソート
        //
        $order_bys = ( isset( $request->order_by )) ? $request->order_by : [];
        $asc_desc  = ( isset( $request->asc_desc )) ? $request->asc_desc : [];
        foreach( $order_bys as $i => $order_by ) {
            if( ! empty( $order_by )) {
                $order_by = ( $order_by == "time" ) ? "start" : $order_by;
                $sort = ( ! empty( $asc_desc[ $i ] )) ? $asc_desc[ $i ] : "asc";
                $schedules = $schedules->orderBy( $order_by, $sort );
            }
        }
        


        // if_debug( $schedules );
        $schedules = $schedules->get();
        // if_debug( $schedules );

        return $schedules;
    }
    
    //　日報検索
    //
    private static function getReports( Request $request ) {

        if( ! is_array( $request->report_lists )) { return collect( [] ); }


        //　検索対象カレンダー
        //
        // $reports = new Report;
        $reports = Report::whereIn( 'report_list_id', $request->report_lists )
                         ->with( [ 'user', 'user.dept', 'report_list.report_prop' ]);
    
        
        //　キーワード検索（件名 or 備考）
        //
        if( $request->keyword ) {
            $keyword = '%' . addcslashes( $request->keyword, '%_\\') . '%';
            $reports = $reports->where( function( $query ) use( $keyword ) {
                                                  $query->where(   'name', 'like', $keyword )
                                                        ->orWhere( 'memo', 'like', $keyword );
                        });
        }

        //　期間の検索
        //
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        if( $start_date and $end_date ) {        
            $reports = $reports->where( function( $sub_query ) use ( $start_date, $end_date ) {
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
        } elseif( ! $start_date and $end_date ) {
            $reports = $reports->where( 'start_date', '<=', $end_date );
        } elseif( $start_date and ! $end_date ) {
            $reports = $reports->where( 'end_date', '>=', $start_date );
        }
        
        //　部署・社員検索
        //
        // if( $request->users and $request->depts ) {
        //     $sub_query = User::select('id')->whereIn( 'id', $request->users )
        //                                   ->orWhere( function( $query ) use ( $request ) {
        //                                         $query->whereIn( 'dept_id', $request->depts );
        //                                   });
        //     $reports = $reports->whereIn( 'user_id', $sub_query );
        // } elseif( ! $request->users and $request->depts ) {
        //     $reports = $reports->whereHas( 'user', function( $query ) use ( $request ) {
        //                                 $query->whereIn( 'dept_id', $request->depts );
        //                     });
        // } elseif( $request->users and ! $request->depts ) {
        //     $reports = $reports->whereIn( 'user_id', $request->users );
        // }

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
    
                // 　日報作成者のみ検索
                //
                $reports = $reports->whereIn( 'user_id', $user_ids );
                $reports = $reports->with( [ 'users' => function( $query ) { $query->where( 'id', -1 ); } ]);
    
            } else {
    
                // 　日報作成者と関連社員で検索
                //
                $reports = $reports->where( function( $query ) use( $user_ids ) {
                                                        $query->whereIn( 'user_id', $user_ids )
                                                              ->orWhere( function( $query2 ) use ( $user_ids ) {
                                                                    $query2->whereHas( 'users', function( $query3 ) use( $user_ids )  {
                                                                                $query3->whereIn( 'id', $user_ids );
                                                                            });
                                                                    });
                                                        });
                $reports = $reports->with( [ 'users' => function( $query ) use( $user_ids ) { $query->whereIn( 'id', $user_ids ); }] );
            }
        } else {
            $reports = $reports->with( [ 'users' => function( $query ) { $query->where( 'id', -1 ); } ]);
        }


        //　関連顧客で検索
        //
        if( $request->customers ) {
            $reports = $reports->whereHas( 'customers', function( $query ) use ( $request ) {
                                        $query->whereIn( 'id', $request->customers ); 
                                    });
        }


        // if_debug( $reports );
        $reports = $reports->get();
        // if_debug( $reports );

        return $reports;
    }
    
    
    //　タスクを検索
    //
    private static function getTasks( Request $request ) {
        
        if( ! is_array( $request->tasklists )) { return collect([]); }

        //　検索対象タスクリスト
        //
        $tasks = Task::whereIn( 'tasklist_id', $request->tasklists )
                     ->with( [ 'user', 'user.dept', 'tasklist.taskprop'] );
        
        //　キーワード検索（件名 or 備考）
        //
        if( $request->keyword ) {
            $keyword = '%' . addcslashes( $request->keyword, '%_\\') . '%';
            $tasks = $tasks->where( function( $query ) use( $keyword ) {
                                                    $query->where(   'name', 'like', $keyword )
                                                          ->orWhere( 'memo', 'like', $keyword );
                        });
        }


        //　期間の検索
        //
        $start_date = $request->start_date;
        $end_date   = $request->end_date;
      
        if( $start_date and $end_date ) {        
            $tasks = $tasks->where( 'due_date', '>=', $start_date )
                                   ->where( 'due_date', '<=', $end_date   );
        } elseif( ! $start_date and $end_date ) {
            $tasks = $tasks->where( 'due_date', '<=', $end_date );
        } elseif( $start_date and ! $end_date ) {
            $tasks = $tasks->where( 'due_date', '>=', $start_date );
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
        
        //　ステータスで検索
        //
        if( ! empty( $request->task_status )) {
            $tasks = $tasks->where( 'status', $request->task_status );
        }
        
        //　ソート
        //
        $order_bys = ( isset( $request->order_by )) ? $request->order_by : [];
        $asc_desc  = ( isset( $request->asc_desc )) ? $request->asc_desc : [];
        foreach( $order_bys as $i => $order_by ) {
            if( ! empty( $order_by )) {
                $order_by = ( $order_by == "time" ) ? "due_time" : $order_by;
                $sort = ( ! empty( $asc_desc[ $i ] )) ? $asc_desc[ $i ] : "asc";
                $tasks = $tasks->orderBy( $order_by, $sort );
            }
        }
  
        // if_debug( $tasks );
        $tasks = $tasks->get();
        // if_debug( $tasks );

        return $tasks;
    } 

    //
    //　出力データの整形
    //
    private static function arrange_outputs( Request $request, $schedules, $tasks, $reports ) {

        $schedule_ids = $schedules->pluck('id')->toArray();
        $tasks_ids    = $tasks->pluck('id')->toArray();
        $report_ids   = $reports->pluck( 'id' )->toArray();
    
        // if_debug( $schedule_ids, $tasks_ids );
    
        $order_bys = ( isset( $request->order_by )) ? $request->order_by : [];
        $asc_desc  = $request->asc_desc;
    
        // $schedules = $schedules->toQuery();
        // $tasks     = $tasks->toQuery();
        
        // if_debug( $schedules, $tasks );
        // $objects = $schedules->selectRaw( '"schedule" as type, id, name, start as time' )
        //       ->union( $tasks->selectRaw( '"task" as type, id,name,due_time as time'    ))
        //       ->orderBy( 'time' );
        //       ->get();
        
        //　スケジュールとタスクをペジネーション
        //
        $objects = DB::table(            'schedules' )->whereIn( 'id', $schedule_ids )->selectRaw( '"schedule" as type, id, name, start    as time, user_id ' )
                     ->union( DB::table( 'tasks'     )->whereIn( 'id', $tasks_ids    )->selectRaw( '"task"     as type, id, name, due_time as time, user_id ' ))
                     ->union( DB::table( 'reports'   )->whereIn( 'id', $report_ids   )->selectRaw( '"report"   as type, id, name, start    as time, user_id ' ));
        
        //　ソート
        //
        foreach( $order_bys as $i => $order_by ) {
            if( ! empty( $order_by )) {
                $sort = ( ! empty( $asc_desc[ $i ] )) ? $asc_desc[ $i ] : "asc";
                $object = $objects->orderBy( $order_by, $sort );
            }
        }
        
        // if_debug( $objects );
        $objects = $objects->paginate( $request->pagination );
        
        // if_debug( $objects );

        return [ 'data'      => $objects, 
                 'schedules' => $schedules, 
                 'tasks'     => $tasks,
                 'reports'   => $reports,
                 ];
        
    }

}

