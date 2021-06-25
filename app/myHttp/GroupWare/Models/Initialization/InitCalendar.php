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
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;

use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;

class InitCalendar  {
    
    // protected $table = 'groups';
    
    //　新規ユーザ作成などの時 or ログイン時にコールされる
    //  ユーザに対応するCalPropが存在しない場合は生成する 
    //
    public static function forUser( $user ) {
        $user = ( $user instanceof User ) ? $user : User::find( $user->id );
        
        $calendars = Calendar::whereDoesntHave( 'calprops', function( $query ) use( $user ) {
                    $query->where( 'user_id', $user->id );
                })->get();
        foreach( $calendars as $calendar ) {
            self::init( $calendar, [ $user ] );
        }

    }
    
    //　新規にCalendar作成した後で呼び出される
    //  全ユーザ分のCalPropを生成する
    //
    public static function withCalendar( Calendar $calendar ) {
        $users = User::all();
        return self::init( $calendar, $users );
    }
    
    //　CalPropを生成
    //
    private static function init( Calendar $calendar, $users ) {

        $values  = [ 'calendar_id'     => $calendar->id, 
                     'name'               => $calendar->name,
                     'memo'               => $calendar->memo,
                     'text_color'         => CalProp::default_text_color(),
                     'background_color'   => CalProp::default_background_color(),
                     'default_permission' => $calendar->default_permission,
                ];
        
        $cal_props = []; 
        foreach( $users as $user ) {
            $values['user_id'] = $user->id;
            $cal_prop = CalProp::create( $values );
            $cal_props[ $cal_prop->id ] = $cal_prop;
        }
        return $cal_props;
    }

}

