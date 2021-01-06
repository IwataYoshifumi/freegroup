<?php

namespace App\myHttp\GroupWare\Controllers\Search;

use Illuminate\Http\Request;
use DB;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\AccessListUserRole;
use App\myHttp\GroupWare\Models\Actions\AccessListAction;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;

use App\myHttp\GroupWare\Requests\AccessListRequest;
use App\myHttp\GroupWare\Requests\DeleteAccessListRequest;

class GetCalendarForScheduleInput {

    //　スケジュール入力フォームで書き込み可能なカレンダーを検索する ( view の schedule.input で利用 )
    //
    public static function user( $user_id ) {
        
        $subquery_1 = CalProp::select( 'calendar_id' )->where( 'user_id', $user_id )->where( 'not_use', '!=', 1 );
        $writables  = Calendar::getCanWrite( $user_id )->pluck('id')->toArray(); 
        $query      = Calendar::where( 'not_use', '!=', 1 )->where( 'disabled', '!=', 1 )->whereIn( 'id', $subquery_1 )->whereIn( 'id', $writables );
        $calendars  = $query->get();
        
        // dump( $calendars, $query );
        
        return $calendars;
    }

    
}
