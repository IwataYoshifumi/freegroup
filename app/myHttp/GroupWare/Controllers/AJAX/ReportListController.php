<?php

namespace App\myHttp\GroupWare\Controllers\AJAX;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

use Exception;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;

use App\myHttp\GroupWare\Controllers\Search\SearchReportList;

class ReportListController extends Controller   {

    public function search( Request $request ) {

        $report_lists = SearchReportList::search( $request );

        $return = [];
        foreach( $report_lists as $r ) {
            array_push( $return,  [  
                'id'    => $r->id, 
                'name'  => $r->name,
                'type'  => $r->type,
            ] );
        }
        
        // if_debug( $request->all(), $report_lists->toArray(), $return );
        
        return response()->json( $return );
    }
    
    
    public function searchForCheckboxes( Request $request ) {
        
        // if_debug( $request->all() );
        
        $report_lists = ReportList::with( 'report_prop' );
        
        //  アクセスリスト権限検索
        //
        // if( $request->auth == 'owner' ) {
        if( $request->report_list_permission == 'owner' ) {
            $query = ReportList::getOwner( user_id() );
        } elseif( $request->auth == 'reader' ) {
            $query = ReportList::getCanRead( user_id() );
        } else { 
            $query = ReportList::getCanWrite( user_id() );
        }
        $ids = toArray( $query, 'id' );
        $report_lists = $report_lists->whereIn('id', $ids );

        //　公開種別の検索
        //
        if( $request->types ) {
            $report_lists = $report_lists->whereIn( 'type', $request->types );
        }
        
        //　Disableの検索
        //
        if( ! $request->show_disabled ) { 
            $report_lists = $report_lists->where( 'disabled', 0 ); 
        }

        //　非表示タスクの表示（report_propを検索）
        //
        
        if( ! $request->show_hidden_report_lists ) {
            $report_lists = $report_lists->whereHas( 'report_prop', function( $query ) {
                            $query->where( 'hide', 0 );
            });
        }

        // dump( $report_lists );
        $report_lists = $report_lists->get();
        $return = [];
        // dump( $report_lists );
        
        foreach( $report_lists as $report_list ) {
            $report_prop = $report_list->my_report_prop();
            $values = [ 'id' => $report_list->id,
                        'report_prop_id' => $report_prop->id,
                        'name' => $report_list->name,
                        'type' => $report_list->type,
                        'not_use' => $report_list->not_use,
                        'disabled' => $report_list->disabled,
                        'prop_name' => $report_prop->name,
                        'hide' => $report_prop->hide,
                        'text_color' => $report_prop->text_color,
                        'background_color' => $report_prop->background_color
            ];
            // $return[$i] = $values;
            // $i++;
            array_push( $return, $values );
        }

        return response()->json( $return );
        
    }
}