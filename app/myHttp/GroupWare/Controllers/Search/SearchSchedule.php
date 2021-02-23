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
        $schedules_3 = clone $schedules;  // 全社公開カレンダー用

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
        
        $clone_users = clone $users; if_debug( $clone_users->select( 'id', 'name' )->get()->toArray() );

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

        /*
         *
         *　検索対象のカレンダーを検索
         *
         */

        //　閲覧制限付きカレンダーを権限で検索あるカレンダーを検索
        //
        if( is_null( $request->calendar_types ) or in_array( 'private', $request->calendar_types )) {
            if( $request->calendar_auth == 'owner' ) {
                $calendars = Calendar::getOwner( user_id() );
            } elseif( $request->calendar_auth == 'writer' ) {
                $calendars = Calendar::getCanWrite( user_id() );
            // } elseif( $request->calendar_auth == 'reader' ) {
            } else {
                $calendars = Calendar::getCanRead( user_id() );
            }
            
            if( count( $calendars )) {
                $private_calendars = clone $calendars->toQuery()->where( 'type', 'private' );
            } else {
                // エラー対策　LogicException Unable to create query for empty collection.
                // カレンダーが何も定義されていないとエラーになる
                //
                $private_calendars = Calendar::whereNull( 'id' );
            }

        } else {
            $private_calendars = Calendar::whereNull( 'id' );   
        }
        
        // 公開カレンダーの検索
        //
        if( is_null( $request->calendar_types ) or in_array( 'public', $request->calendar_types )) {
            // $public_calendars = Calendar::whereInOwners( $users )->where( 'type', 'public' );
            $public_calendars = Calendar::where( 'type', 'public' );
        } else {
            $public_calendars = Calendar::whereNull( 'id' );
        }

        //　全社公開カレンダーの検索
        //
        
        if( is_null( $request->calendar_types ) or in_array( 'company-wide', $request->calendar_types )) {
            $campany_wide_calendars = Calendar::where( 'type', 'company-wide' );
        }
        
        //　カレンダーの公開種別の検索（公開、閲覧制限、全社公開）        
        // $calendars = $calendars->toQuery();
        // if( is_array( $request->calendar_types )) {
        //     $calendars->whereIn( 'type', $request->calendar_types );
        // }

        //　CalPropでhide にしているカレンダーは検索対象外
        //
        if( ! $request->show_hidden_calendar ) {
            $private_calendars->whereHas( 'calprops', function( $query ) {
                        $query->where( 'user_id', user_id() )->where( 'hide', 0 ); 
                    });
            $public_calendars->whereHas( 'calprops', function( $query ) {
                        $query->where( 'user_id', user_id() )->where( 'hide', 0 ); 
                    });
            $campany_wide_calendars->whereHas( 'calprops', function( $query ) {
                        $query->where( 'user_id', user_id() )->where( 'hide', 0 ); 
                    });
        }
        
        //　検索カレンダーを検索
        //
        // if_debug( $private_calendars->get()->toArray(), $public_calendars->get()->toArray(), $campany_wide_calendars->get()->toArray() );
        $calendars = $private_calendars->union( $public_calendars )->get();

        // エラー対策　LogicException Unable to create query for empty collection.
        // カレンダーが何も定義されていないとエラーになる
        //
        if( count( $calendars )) {
            $calendars = $calendars->toQuery()->select( 'id' );
        } else {
            $calendars = Calendar::whereNull( 'id' )->select( 'id' );
        }
        $campany_wide_calendars->select( 'id' );

        //　対象カレンダーの予定を検索
        //
        $schedules_1->whereIn( 'calendar_id', $calendars );
        $schedules_2->whereIn( 'calendar_id', $calendars );
        $schedules_3->whereIn( 'calendar_id', $campany_wide_calendars );
        $ids_1 = $schedules_1->get()->pluck( 'id', 'id' );
        $ids_2 = $schedules_2->get()->pluck( 'id', 'id' );
        $ids_3 = $schedules_3->get()->pluck( 'id', 'id' );
        $schedule_ids = $ids_1->union( $ids_2 )->union( $ids_3 )->toArray();

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

        //　検索結果を返す
        //
        $return = [ 
                    'user_ids'  => $user_ids,
                    'users'     => $users,
                    'depts'     => $depts,
                    'schedules' => $schedules,
                    'calendars' => $calendars,
                    'calprops'  => $calprops
                    ];
        
        if_debug( $return );
        return $return;
        
        
    }


}

