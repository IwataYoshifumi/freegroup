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
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;



class SearchSchedule {

    const NULL_RETURN = [ 
            'user_ids'  => [],
            'users'     => [],
            'depts'     => [],
            'schedules' => [],
            'calendars' => [],
            'calprops'  => [] 
            ];

    /*
     *
     *
     */
    static public function search( Request $request ) {
        
        //　検索条件のチェック
        //
        if( ! isset( $request->start_date ) and ! isset( $request->end_date )) { return self::NULL_RETURN; }
        if( ! isset( $request->depts      ) and ! isset( $request->users    )) { return self::NULL_RETURN; }
        
        //　期間の検索
        //
        // if_debug( __METHOD__, $request->start_date, $request->end_date );
        // if_debug( __METHOD__, $request->input('start_date'), $request->input('end_date') );
        // dd( $request->all() );

        $schedules = Schedule::where( function( $sub_query ) use ( $request ) {
                    $sub_query->where( function( $query ) use ( $request ) {
                                $query->where( 'start_date', '>=', $request->start_date )
                                      ->where( 'start_date', '<=', $request->end_date   );
                                });
                    $sub_query->orWhere( function( $query ) use( $request ) {
                                $query->where( 'end_date', '>=', $request->start_date )
                                      ->where( 'end_date', '<=', $request->end_date   );
                                });
                    $sub_query->orWhere( function( $query ) use( $request ) {
                                $query->where( 'start_date', '<', $request->start_date )
                                      ->where( 'end_date',   '>', $request->end_date   );
                                });
                });

        $schedules_1 = clone $schedules;  // 予定作成者用
        $schedules_2 = clone $schedules;  // 関係者検索用

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

        // if( ! empty( $request->user_name )) { $users->where( 'name', 'like', '%'. $request->user_name . '%');  }
        // if_debug( $users, $users->get()->toArray() );

        //　作成者で検索
        //
        $schedules_1->whereIn( 'user_id', $users );
        
        //　関連社員の検索
        //
        $schedules_2->whereHas( 'users', function( $query ) use ( $users ) {
            $query->whereIn( 'id', $users );
        });
        
        //　アクセス権限で権限のあるカレンダーを検索
        //
        if( $request->calendar_auth == 'owner' ) {
            $calendars = Calendar::getOwner( user_id() );
        } elseif( $request->calendar_auth == 'writer' ) {
            $calendars = Calendar::getCanWrite( user_id() );
        } elseif( $request->calendar_auth == 'reader' ) {
            $calendars = Calendar::getCanRead( user_id() );
        }
        $calendars = $calendars->toQuery();
        if( is_array( $request->calendar_types )) {
            $calendars->whereIn( 'type', $request->calendar_types );
        }
        
        //　CalPropでhide にしているカレンダーは検索対象外
        //
        if( ! $request->show_hidden_calendar ) {
            $calendars->whereHas( 'calprops', function( $query ) {
                $query->where( 'user_id', user_id() )->where( 'hide', 0 ); 
            });
        }

        //　アクセス権限のあるカレンダーに登録された予定を検索
        //
        $calendars->select( 'id' );
        $schedules_1->whereIn( 'calendar_id', $calendars );
        $schedules_2->whereIn( 'calendar_id', $calendars );
        $ids_1 = $schedules_1->get()->pluck( 'id', 'id' );
        $ids_2 = $schedules_2->get()->pluck( 'id', 'id' );
        $schedule_ids = $ids_1->union( $ids_2 )->toArray();
        // if_debug( $ids_1, $ids_2, $ids_1->union( $ids_2 ) );

        //
        //
        $schedules = Schedule::whereIn( 'id', $schedule_ids );
        

        // 予定作成者・関連社員のロード
        //
        $schedules->with( ['creator', 'updator'] );
        if( $request->search_condition == 'only_creator' ) {
            // $schedules->with( 'user' );
            $schedules->with( [ 'user' => function( $query ) use ( $users ) { $query->whereIn( 'id', $users ); } ]);
            $schedules->with( ['users' => function( $query ) use ( $users ) { $query->where( 'id', 0 ); } ]);     // 関連社員はロードしない

        } else {
            $schedules->with( [ 'user' => function( $query ) use ( $users ) { $query->whereIn( 'id', $users ); } ]); 
            $schedules->with( ['users' => function( $query ) use ( $users ) { $query->whereIn( 'id', $users ); } ]);
        } 

        $schedules = $schedules->get();
        
        //　対象のカレンダーとCalpropを検索
        //
        $calendars = Calendar::whereIn( 'id', $schedules->pluck( 'calendar_id' )->toArray() )
                             ->with( [ 'calprops' => function( $query ) { 
                                       $query->where( 'user_id', user_id() ); 
                                    }
                              ])->get();
                              
        // キーは calendar_id, 値は calpropのインスタンス
        //
        $calprops = $calendars->pluck( 'calprops.0', 'id' ); 
        // if_debug( $calendars, $calprops );
        
        //　予定で検索されたユーザのみ改めて検索
        //
        $user_ids = [];
        foreach( $schedules as $s ) {
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
                    'schedules' => $schedules,
                    'calendars' => $calendars,
                    'calprops'  => $calprops
                    ];
        
        return $return;
        
        
    }


}

