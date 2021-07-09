<?php
namespace App\myHttp\GroupWare\Models\Initialization;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

use DB;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;

use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;

class InitReportProp  {
    
    // protected $table = 'groups';
    
    //　新規ユーザ作成などの時 or ログイン時にコールされる
    //  ユーザに対応するReportPropが存在しない場合は生成する 
    //
    public static function forUser( $user ) {
        $user = ( $user instanceof User ) ? $user : User::find( $user->id );
        
        $report_lists = ReportList::whereDoesntHave( 'report_props', function( $query ) use ( $user ) {
                    $query->where( 'user_id', $user->id );
                })->get();
        
        foreach( $report_lists as $report_list ) {
            self::init( $report_list, [ $user ] );
        }

    }
    
    //　新規にReportList作成した後で呼び出される
    //  全ユーザ分のReportPropを生成する
    //
    public static function withReportList( ReportList $report_list ) {
        $users = User::all();
        return self::init( $report_list, $users );
    }
    
    //　ReportPropを精製
    //
    private static function init( ReportList $report_list, $users ) {

        $values  = [ 'report_list_id'     => $report_list->id, 
                     'name'               => $report_list->name,
                     'memo'               => $report_list->memo,
                     'text_color'         => ReportProp::default_text_color(),
                     'background_color'   => ReportProp::default_background_color(),
                     'default_permission' => $report_list->default_permission,
                ];
        
        $report_props = []; 
        foreach( $users as $user ) {
            $values['user_id'] = $user->id;
            $report_prop = ReportProp::create( $values );
            $report_props[ $report_prop->id ] = $report_prop;
        }
        return $report_props;
    }

}

