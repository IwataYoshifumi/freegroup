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
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Calendar;

use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;

use App\myHttp\GroupWare\Events\UserCreateEvent;
use App\myHttp\GroupWare\Events\UserRetireEvent;
use App\myHttp\GroupWare\Events\UserReturnEvent;
use App\myHttp\GroupWare\Events\UserTransferDeptEvent;

use App\myHttp\GroupWare\Models\Initialization\InitCalendar;
use App\myHttp\GroupWare\Models\Initialization\InitReportProp;

use App\myHttp\GroupWare\Notifications\GoogleCalendar\UnSyncGoogleCalendar;

class InitUser  {
    
    // protected $table = 'groups';
    
    //　検索
    //
    //　何かしらのエラーがあれば false を返す
    //
    public static function init( $user ) {

        
        $user = ( $user instanceof User ) ? $user : User::find( $user );
        $has_error = false;

        //　ロールグループの設定がなければデフォルトのロールグループを割当
        //
        if( ! $user->hasRoleGroup() ) {

            //　エラー対策（デフォルトRoleGroupの定義がないとエラーになってしまう対策）
            //
            if( RoleGroup::hasDefault() ) {
                $user->setRoleGroup( RoleGroup::getDefault() );
            } else {
                //　デフォルトのRoleGroupがなければログアウト
                //
                session()->flash( 'error_message', "デフォルトのロールが設定されていません。管理者にデフォルトロールグループの作成とロール割当を依頼してください。" );
                $has_error = true;                
            }
        }
        
        //　Calprop, ReportPropクラスの初期化
        //
        InitCalendar::forUser( $user );
        InitReportProp::forUser( $user );
        
        //　アクセス権がなくなったカレンダーのGoogleカレンダー同期を解除
        //
        self::unSyncGoogleCalendars( $user );

        return ! $has_error;
    }
    
    //  アクセス権限がなくなったカレンダーのCalPropに対して、Googleカレンダー同期を解除する
    //
    public static function unSyncGoogleCalendars( User $user ) {

        //　制限付きカレンダーで読み出し権限がなくなったカレンダーを検索
        //
        $calendars_cannot_read = Calendar::whereCanNotRead( $user )->where( 'type', 'private' )->select( 'id' );
        $calprops = CalProp::where( 'user_id', $user->id )
                           ->where( 'google_sync_on', 1 )
                           ->whereIn( 'calendar_id', $calendars_cannot_read )
                           ->get();        
        
        //　閲覧権のなくなったカレンダーの同期を解除
        //
        DB::transaction( function() use ( $user, $calprops ) {
            
            foreach( $calprops as $calprop ) {
                $calprop->google_sync_on = 0;
                $calprop->google_sync_check = 0;
                $calprop->save();

                //　同期解除になった旨をメール通知
                //
                $user->notify( new UnSyncGoogleCalendar( $calprop ));
            }
        });
        
    }
    

}

