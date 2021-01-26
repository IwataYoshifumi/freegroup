<?php

namespace App\myHttp\GroupWare\Controllers\SubClass;
          
use Illuminate\Http\Request;
use DB;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\AccessListUserRole;
use App\myHttp\GroupWare\Models\Actions\AccessListAction;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;

use App\myHttp\GroupWare\Requests\AccessListRequest;
use App\myHttp\GroupWare\Requests\DeleteAccessListRequest;

class GetReportListForReportInput {
    //　スケジュール入力フォームで書き込み可能なカレンダーを検索する ( view の schedule.input で利用 )
    //
    public static function user( $user_id ) {
        
        $subquery_1 = ReportProp::select( 'report_list_id' )->where( 'user_id', $user_id )->where( 'not_use', '!=', 1 );
        $writables  = ReportList::getCanWrite( $user_id )->pluck('id')->toArray(); 
        $query      = ReportList::where( 'not_use', '!=', 1 )->where( 'disabled', '!=', 1 )->whereIn( 'id', $subquery_1 )->whereIn( 'id', $writables );
        $report_lists  = $query->get();
        
        // if_debug( $report_lists, $query );
        
        return $report_lists;
    }
    
    public static function getFromUserAndReportList( $user_id, ReportList $report_list ) {
        $report_lists = self::user( $user_id );
        $report_lists->push( $report_list );

        return $report_lists;
        
    }

    
}
