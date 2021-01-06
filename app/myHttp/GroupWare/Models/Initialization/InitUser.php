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

use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;

use App\myHttp\GroupWare\Events\UserCreateEvent;
use App\myHttp\GroupWare\Events\UserRetireEvent;
use App\myHttp\GroupWare\Events\UserReturnEvent;
use App\myHttp\GroupWare\Events\UserTransferDeptEvent;


class InitUser  {
    
    // protected $table = 'groups';
    
    //　検索
    //
    public static function init( User $user ) {
        
        //　ロールグループの設定がなければデフォルトのロールグループを割当
        //
        if( ! $user->hasRoleGroup() ) {
            $user->setRoleGroup( RoleGroup::getDefault() );
        }
        self::initCalendars( $user );
        self::checkCalenderAccessToUnsyncGoogleCalendar( $user );
    }
    
    //　アクセスできるカレンダーで、CalPropがない場合は、CalPropを新規作成
    //
    public static function initCalendars( User $user ) {
        
        
    }

    //  アクセス権限がなくなったカレンダーのCalPropに対して、Google同期解除（ $calProp->no_auth, とGoogle の同期解除設定
    //
    public static function checkCalenderAccessToUnsyncGoogleCalendar( User $user ) {
        Log::debug( __METHOD__ );
    }
    

}

