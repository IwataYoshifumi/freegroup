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
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;



class SearchReport {

    const NULL_RETURN = [ 
            'user_ids'  => [],
            'users'     => [],
            'depts'     => [],
            'reports'   => [],
            'report_lists' => [],
            'report_props' => [],
            ];

    /*
     *
     *
     */
    static public function search( Request $request ) {
        
        //　検索条件のチェック
        //

        if( ! isset( $request->start_date ) and ! isset( $request->end_date ) and ! $request->report_list_id ) { return self::NULL_RETURN; }
        if( ( ! isset( $request->depts ) and ! isset( $request->users )) and 
            ( ! ( isset( $request->report_lists )) and ! $request->report_list_id ))
            { return self::NULL_RETURN; }
        
        if( isset( $request->report_list_id )) {
            if( ! isset( $request->sorts )) { $request->sorts = [ 'created_at' ]; }
        }
        
        //　期間の検索
        //

        if( $request->start_date and $request->end_date ) {
            if( $request->search_date_condition == 'created_at' ) {
                $reports = Report::where( function( $query ) use ( $request ) {
                        $query->where( 'created_at', '>=', $request->start_date . ' 00:00:00' )
                              ->where( 'created_at', '<=', $request->end_date   . ' 23:59:59' );
                });
                
            } else {
                $reports = Report::where( function( $sub_query ) use ( $request ) {
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
            }
            
        } else {
            $reports = Report::select( '*' );
        }
        
        if( $request->report_list_id ) {
            $reports->where( 'report_list_id', $request->report_list_id );
        }
        
        //　キーワード検索（件名・備考検索）
        //
        if( ! empty( $request->key_word )) {
            $reports->where( function( $query ) use( $request ) {
                $q = "%". $request->key_word . "%";
                $query->where(   'name', 'like', $q )
                      ->orWhere( 'memo', 'like', $q );
            });
        }

        $reports_1 = clone $reports;  // 予定作成者用
        $reports_2 = clone $reports;  // 関係者検索用

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
        
        $reports_1->whereIn( 'user_id', $users );
        
        //　関連社員の検索
        //
        $reports_2->whereHas( 'users', function( $query ) use ( $users ) {
            $query->whereIn( 'id', $users );
        });

        /*
         *
         *　検索対象の日報リストを検索
         *
         */

        //　閲覧制限付き日報リストを権限で検索ある日報リストを検索
        //
        if( is_null( $request->report_list_types ) or in_array( 'private', $request->report_list_types )) {
            if( $request->report_list_auth == 'owner' ) {
                $report_lists = ReportList::getOwner( user_id() );
            } elseif( $request->report_list_auth == 'writer' ) {
                $report_lists = ReportList::getCanWrite( user_id() );
            // } elseif( $request->report_list_auth == 'reader' ) {
            } else {
                $report_lists = ReportList::getCanRead( user_id() );
            }
            $private_report_lists = clone $report_lists->toQuery()->where( 'type', 'private' );

        } else {
            $private_report_lists = ReportList::where( 'id', 0 );   
        }
        
        // 公開日報リストの検索
        //
        if( is_null( $request->report_list_types ) or in_array( 'public', $request->report_list_types )) {
            // $public_report_lists = ReportList::whereInOwners( $users )->where( 'type', 'public' );
            $public_report_lists = ReportList::where( 'type', 'public' );
        } else {
            $public_report_lists = ReportList::where( 'id', 0 );
        }
        
        //　日報リストの公開種別の検索（公開、閲覧制限、全社公開）        
        //
        $report_lists = $report_lists->toQuery();
        if( is_array( $request->report_list_types )) {
            $report_lists->whereIn( 'type', $request->report_list_types );
        }

        //　ReportPropでhide にしている日報リストは検索対象外
        //
        if( ! $request->show_hidden_report_list ) {
            $private_report_lists->whereHas( 'report_props', function( $query ) {
                        $query->where( 'user_id', user_id() )->where( 'hide', 0 ); 
                    });
            $public_report_lists->whereHas( 'report_props', function( $query ) {
                        $query->where( 'user_id', user_id() )->where( 'hide', 0 ); 
                    });
        }
        
        //　日報リストを検索
        //
        // if_debug( $private_report_lists->get()->toArray(), $public_report_lists->get()->toArray(), $campany_wide_report_lists->get()->toArray() );
        $report_lists = $private_report_lists->union( $public_report_lists )->get();

        $report_lists = $report_lists->toQuery()->select( 'id' );

        //　対象日報リストの予定を検索
        //
        $reports_1->whereIn( 'report_list_id', $report_lists );
        $reports_2->whereIn( 'report_list_id', $report_lists );
        $ids_1 = $reports_1->get()->pluck( 'id', 'id' );
        $ids_2 = $reports_2->get()->pluck( 'id', 'id' );
        $report_ids = $ids_1->union( $ids_2 )->toArray();

        $reports = Report::whereIn( 'id', $report_ids );

        // 予定作成者・関連社員のロード
        //
        $reports->with( ['creator', 'updator'] );
        if( $request->search_condition == 'only_creator' ) {
            // $reports->with( [ 'user' => function( $query ) use ( $users ) { $query->whereIn( 'id', $users ); } ]);
            $reports->with( 'user' );
            $reports->with( ['users' => function( $query ) use ( $users ) { $query->where( 'id', 0 ); } ]);     // 関連社員はロードしない

        } else {
            // $reports->with( [ 'user' => function( $query ) use ( $users ) { $query->whereIn( 'id', $users ); } ]);
            $reports->with( 'user' );
            $reports->with( ['users' => function( $query ) use ( $users ) { $query->whereIn( 'id', $users ); } ]);
        }
        
        //　ソート
        //
        if( $request->sorts and is_array( $request->sorts )) {
            foreach( $request->sorts as $sort ) {
                if( $sort ) {
                    $reports->orderby( $sort, 'desc' );
                }
            }
        }
        
        //　ペジネーション
        //
        if( ! is_null( $request->pagination ) and $request->pagination >= 1 ) {
            $reports = $reports->paginate( $request->pagination );
        } else {
            $reports = $reports->get();
        }
        
        //　対象の日報リストとReport Propを検索
        //
        $report_lists = ReportList::whereIn( 'id', $reports->pluck( 'report_list_id' )->toArray() )
                             ->with( [ 'report_props' => function( $query ) { 
                                       $query->where( 'user_id', user_id() ); 
                                    }
                              ])->get();
                              
        // キーは report_list_id, 値は report_propのインスタンス
        //
        $report_props = $report_lists->pluck( 'report_props.0', 'id' ); 
        // if_debug( $report_lists, $report_props );
        
        //　予定で検索されたユーザのみ改めて検索
        //
        $user_ids = [];
        foreach( $reports as $s ) {
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
                    'reports' => $reports,
                    'report_lists' => $report_lists,
                    'report_props'  => $report_props
                    ];
        
        return $return;
        
        
    }


}

