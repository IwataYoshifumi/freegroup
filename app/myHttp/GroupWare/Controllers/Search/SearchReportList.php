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



class SearchReportList {

    const NULL_RETURN = [ 
            // 'report_lists' => [],
            ];

    /*
     * 検索条件
     * 
     * name  : 日報リスト名（ReportPropのnameを一部検索）
     * users：user_id の配列：アクセス権検索対象のユーザ
     * show_hidden : boolean ReportProp でhideになっている日報リストも検索
     * show_disababled : boolean ReportListのdisabled になっている日報リストも検索
     * user_auth : owner, canWrite, canRead 日報リストに対するユーザのアクセス権限を検索
     * types : public, private : 日報リストの公開種別で検索
     *
     */
    static public function search( Request $request ) {

        // if_debug( $request->all() );        
        //　検索条件のチェック
        //
        $auth = auth( 'user' )->user();
        
        // 検索条件のチェック
        //
        // if( ! is_debug() and ! is_array( $request->users )) { return self::NULL_RETURN; }
        
        //　検索条件の初期化
        //
        $users = ( is_array( $request->users )) ? $request->users : [ $auth->id ];
        if( ! is_debug() and ! in_array( $auth->id, $users )) { array_push( $users, $auth->id ); }

        // 日報リスト属性（ReportProp）の検索（表示名、非表示）
        // 制約Eagerロード
        //
        $report_lists = ReportList::whereHas( 'report_props', function( $query ) use ( $request ) {
                                            $query->where( 'user_id', user_id() );
            if(   $request->name )        { $query->where( 'name', 'like', '%'. $request->name . '%' ); }
            if( ! $request->show_hidden ) { $query->where( 'hide', 0 );                                 }
        });
        // $report_lists->with([ 'report_props' => function( $query ) { $query->where( 'user_id', user_id() ); }]);
        $report_lists->with( 'report_prop' );

        //　デフォルトでは、無効にした日報リストは検索対象外
        //
        if( ! $request->show_disabled ) {
            $report_lists->where( 'disabled', 0 );
        }
        
        //　公開日報リストの検索用
        //
        $public_report_lists = clone $report_lists;
        $public_report_lists->where( 'type', 'public' );
        
        //　アクセスリストの検索
        //
        if( $request->user_auth == "canRead" ) {
            $subquery = ReportList::whereInUsersCanRead( $users )->select( 'id' );
            $report_lists->whereIn( 'id', $subquery );
        } elseif( $request->user_auth == "canWrite" ) {
            $subquery = ReportList::whereInUsersCanWrite( $users )->select( 'id' );
            $report_lists->whereIn( 'id', $subquery );
        } else { // owner
            $subquery = ReportList::whereInOwners( $users )->select( 'id' );
            $report_lists->whereIn( 'id', $subquery );
        }
        
        //　日報リストの公開種別で検索
        //
        if( is_array( $request->types )) {
            $report_lists->whereIn( 'type', $request->types );
        }
        
        
        
        //　公開　日報リストの検索
        //
        if( $request->user_auth == "canRead" and ( is_null( $request->types ) or in_array( 'public', $request->types ))) {
            $report_lists = $report_lists->union( $public_report_lists );
        }
        
        
        
        // if_debug( $report_lists, $users );
        $return = $report_lists->get();
        
        return $return;
        
        
    }


}

