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
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;



class SearchTask {

    const NULL_RETURN = [ 
            'user_ids'  => [],
            'users'     => [],
            'depts'     => [],
            'tasks'   => [],
            'tasklists' => [],
            'taskprops' => [],
            ];

    /*
     *
     *
     */
    static public function search( Request $request ) {
        
        //　検索条件のチェック
        //

        if( ! isset( $request->start_date ) and ! isset( $request->end_date ) and ! isset( $request->status )) { return self::NULL_RETURN; }
        if( ( ! isset( $request->depts ) and ! isset( $request->users )) and 
            ( ! ( isset( $request->tasklists )) and ! $request->tasklist_id ))
            { return self::NULL_RETURN; }
        
        if( isset( $request->tasklist_id )) {
            if( ! isset( $request->sorts )) { $request->sorts = [ 'due_date' ]; }
        }
        
        //　タスクの期限の検索
        //
        if( $request->start_date or $request->end_date ) {
            $tasks = Task::where( function( $sub_query ) use ( $request ) {
                        $sub_query->where( function( $query ) use ( $request ) {
                            if( $request->start_date ) {
                                $query->where( 'due_date', '>=', $request->start_date );
                            }
                            if( $request->end_date ) {
                                $query->where( 'due_date', '<=', $request->end_date   );
                            }
                        });
                    });
        } else {
            $tasks = Task::select( '*' );
        }
        
        if( $request->status ) {
            $tasks = $tasks->where( 'status', $request->status );
        }
         
        
        if( is_array( $request->tasklists )) {
            $tasks->whereIn( 'tasklist_id', $request->tasklists );
        }

        $tasks_1 = clone $tasks;  // 予定作成者用
        $tasks_2 = clone $tasks;  // 関係者検索用

        //　社員、部署検索
        //
        $users = User::select( 'id' );
        
        //　退社社員の予定は検索対象外
        //
        if( ! $request->show_retired_users ) { $users->where( 'retired', 0 ); }

        //　検索対象の社員ＩＤｓのクエリー作成
        //
        if( is_array( $request->depts ) and is_array( $request->users )) {
                $users->where( function( $query ) use ( $request ) {
                                $query->whereIn( 'dept_id', $request->depts )
                                      ->orWhereIn( 'id'   , $request->users );
                } );   
        } elseif( is_array( $request->depts ) and ! is_array( $request->users )) {
            $users->whereIn( 'dept_id', $request->depts ); 
        
        } elseif( ! is_array( $request->depts ) and is_array( $request->users )) {
            $users->whereIn( 'id',      $request->users ); 
        }
        $clone_users = clone $users;


        //　作成者で検索
        //
        
        $tasks_1->whereIn( 'user_id', $users );
        
        //　関連社員の検索
        //
        $tasks_2->whereHas( 'users', function( $query ) use ( $users ) {
            $query->whereIn( 'id', $users );
        });

        /*
         *
         *　検索対象のタスクリストを検索
         *
         */

        //　閲覧制限付きタスクリストを権限で検索あるタスクリストを検索
        //
        if( is_null( $request->tasklist_types ) or in_array( 'private', $request->tasklist_types )) {
            if( $request->tasklist_auth == 'owner' ) {
                $tasklists = TaskList::getOwner( user_id() );
            } elseif( $request->tasklist_auth == 'writer' ) {
                $tasklists = TaskList::getCanWrite( user_id() );
            // } elseif( $request->tasklist_auth == 'reader' ) {
            } else {
                $tasklists = TaskList::getCanRead( user_id() );
            }
            $private_tasklists = clone $tasklists->toQuery()->where( 'type', 'private' );

        } else {
            $private_tasklists = TaskList::where( 'id', 0 );   
        }
        
        // 公開タスクリストの検索
        //
        if( is_null( $request->tasklist_types ) or in_array( 'public', $request->tasklist_types )) {
            // $public_tasklists = TaskList::whereInOwners( $users )->where( 'type', 'public' );
            $public_tasklists = TaskList::where( 'type', 'public' );
        } else {
            $public_tasklists = TaskList::where( 'id', 0 );
        }
        
        //　タスクリストの公開種別の検索（公開、閲覧制限、全社公開）        
        //
        $tasklists = $tasklists->toQuery();
        if( is_array( $request->tasklist_types )) {
            $tasklists->whereIn( 'type', $request->tasklist_types );
        }

        //　TaskPropでhide にしているタスクリストは検索対象外
        //
        if( ! $request->show_hidden_tasklist ) {
            $private_tasklists->whereHas( 'taskprops', function( $query ) {
                        $query->where( 'user_id', user_id() )->where( 'hide', 0 ); 
                    });
            $public_tasklists->whereHas( 'taskprops', function( $query ) {
                        $query->where( 'user_id', user_id() )->where( 'hide', 0 ); 
                    });
        }
        
        //　タスクリストを検索
        //
        // if_debug( $private_tasklists->get()->toArray(), $public_tasklists->get()->toArray(), $campany_wide_tasklists->get()->toArray() );
        $tasklists = $private_tasklists->union( $public_tasklists )->get();

        $tasklists = $tasklists->toQuery()->select( 'id' );

        //　対象タスクリストの予定を検索
        //
        $tasks_1->whereIn( 'tasklist_id', $tasklists );
        $tasks_2->whereIn( 'tasklist_id', $tasklists );
        $ids_1 = $tasks_1->get()->pluck( 'id', 'id' );
        $ids_2 = $tasks_2->get()->pluck( 'id', 'id' );
        $task_ids = $ids_1->union( $ids_2 )->toArray();

        $tasks = Task::whereIn( 'id', $task_ids );

        // 予定作成者・関連社員のロード
        //
        $tasks->with( ['creator', 'updator', 'complete_user', 'complete_user.dept' ] );
        if( $request->search_condition == 'only_creator' ) {
            // $tasks->with( [ 'user' => function( $query ) use ( $users ) { $query->whereIn( 'id', $users ); } ]);
            $tasks->with( 'user' );
            $tasks->with( ['users' => function( $query ) use ( $users ) { $query->where( 'id', 0 ); } ]);     // 関連社員はロードしない

        } else {
            // $tasks->with( [ 'user' => function( $query ) use ( $users ) { $query->whereIn( 'id', $users ); } ]);
            $tasks->with( 'user' );
            $tasks->with( ['users' => function( $query ) use ( $users ) { $query->whereIn( 'id', $users ); } ]);
        }
        
        //　ソート
        //
        if( $request->sorts and is_array( $request->sorts )) {
            foreach( $request->sorts as $sort ) {
                if( $sort ) {
                    $tasks->orderby( $sort, 'desc' );
                }
            }
        }
        
        //　ペジネーション
        //
        if( ! is_null( $request->pagination ) and $request->pagination >= 1 ) {
            $tasks = $tasks->paginate( $request->pagination );
        } else {
            $tasks = $tasks->get();
        }
        
        //　対象のタスクリストとTask Propを検索
        //
        $tasklists = TaskList::whereIn( 'id', $tasks->pluck( 'tasklist_id' )->toArray() )
                             ->with( [ 'taskprops' => function( $query ) { 
                                       $query->where( 'user_id', user_id() ); 
                                    }
                              ])->get();
                              
        // キーは tasklist_id, 値は taskpropのインスタンス
        //
        $taskprops = $tasklists->pluck( 'taskprops.0', 'id' ); 
        // if_debug( $tasklists, $taskprops );
        
        //　予定で検索されたユーザのみ改めて検索
        //
        $user_ids = [];
        foreach( $tasks as $s ) {
            if( $s->user ) { $user_ids[ $s->user->id ] = $s->user->id; }
            if( ! empty( $s->users ) and count( $s->users ) ) {
                foreach( $s->users as $attendee ) {
                    $user_ids[ $attendee->id ] = $attendee->id;
                }
            }
        }
        // $user_ids = $users->get()->pluck('id', 'id');
        $users    = User::whereIn( 'id', $user_ids )->with( 'dept' )->get();

        //　予定で検索された社員の所属部署を検索
        //
        $depts = Dept::whereHas( 'users', function( $query ) use( $user_ids ) {
                                    $query->whereIn( 'id', $user_ids ); })
                     ->with( ['users' => function( $query ) use( $user_ids ) { 
                            $query->whereIn( 'id', $user_ids ); 
                     }])->get();

        
        // if_debug( $users, $user_ids );
        
        $return = [ 
                    'user_ids'  => $user_ids,
                    'users'     => $users,
                    'depts'     => $depts,
                    'tasks' => $tasks,
                    'tasklists' => $tasklists,
                    'taskprops'  => $taskprops
                    ];
        
        return $return;
        
        
    }


}

