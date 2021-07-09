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
use App\myHttp\GroupWare\Models\AccessListUserRole;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\TaskList;

use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;
use App\myHttp\GroupWare\Models\Actions\AccessListAction;
use App\myHttp\GroupWare\Models\Actions\CalendarAction;
use App\myHttp\GroupWare\Models\Actions\TaskListAction;

use App\myHttp\GroupWare\Events\UserCreateEvent;
use App\myHttp\GroupWare\Events\UserRetireEvent;
use App\myHttp\GroupWare\Events\UserReturnEvent;
use App\myHttp\GroupWare\Events\UserTransferDeptEvent;

use App\myHttp\GroupWare\Models\Initialization\InitCalendar;
use App\myHttp\GroupWare\Models\Initialization\InitTaskList;
use App\myHttp\GroupWare\Models\Initialization\InitReportProp;
use App\myHttp\GroupWare\Models\Initialization\InitTaskProp;

use App\myHttp\GroupWare\Notifications\GoogleCalendar\UnSyncGoogleCalendar;

class InitUser  {
    
    //
    //　何かしらのエラーがあれば false を返す
    //
    //　デフォルトのロールを割り当てる
    //　初期設定の公開カレンダーを自動生成する
    //　公開日報を作成する
    //　非公開タスクリストを作成する
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
        
        //　Calender(CalProp), ReportProp, TaskList(TaskProp) クラスの初期化
        //
        InitCalendar::forUser( $user );
        InitReportProp::forUser( $user );
        InitTaskList::forUser( $user );
        
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


    // ユーザ作成時に初期化処理
    //　自分のみが管理者のアクセスリストを作成
    //　公開カレンダーを作成
    //　非公開タスクリストを作成
    //　管理者権限のみ実行可能
    //
    public static function whenUserHasCreatedFirst( User $user ) {
        
        //　初期化
        //
        self::init( $user );
        
        //　自分のみ管理者のアクセスリストを生成
        //
        $access_list = self::initAccessList( $user );
        
        //　自分のカレンダーを生成
        //　自分のタスクリストを生成
        //
        if( ! is_null( $access_list )) {

            //　自分の公開カレンダーを生成
            //
            self::initCalendar( $user, $access_list );
    
            //　自分のタスクリスト（プライベート）を生成
            //
            self::initTaskList( $user, $access_list );
        }
    }

    //　ユーザ用アクセスリストの初期化（ユーザが管理者のアクセスリストがなければ、作成）
    //　ユーザ自分のみが管理者のアクセスリストを生成
    //
    public static function initAccessList( User $user ) {

        // 管理者権限のアクセスリストがあればアクセスリストを生成せず、
        // 自分が管理者のアクセスリストのインスタンスを返す。
        //
        $access_lists = AccessList::getOwner( $user );

        if( count( $access_lists ) >= 1 ) {

            //　自分のみ管理者であるアクセスリストを検索
            //
            $user_roles = AccessListUserRole::whereIn( 'access_list_id', $access_lists->pluck('id')->toArray() )
                                            ->selectRaw( 'access_list_id, count( user_id ) as count' )
                                            ->having( 'count', 1 )
                                            ->groupBy( 'access_list_id' )->get();
            // dump( $user_roles->toArray() );                
            //　基本は自分のみ設定されているアクセスリストを返す
            //
            if( count( $user_roles )) {
                return AccessList::find( $user_roles->first()->access_list_id );
                
            } else {
                //　自分のみ管理者のアクセスリストがなければ任意のアクセスリストを返す
                //
                return $access_lists->first();
            }
        } 
        
        // 　自分のみ管理者のアクセスリストを生成（同一部署内は閲覧可能）
        //
        $request = new Request;
        $request->name = $user->name . 'のみ管理者（' . $user->dept->name . '内に公開）';
        $request->memo = '初期自動生成';
        
        $i = 1;
        $orders[$i] = $i;
        $roles[$i]  = 'owner';
        $types[$i]  = 'user';
        $users[$i]  = $user->id;
        $i++;
        $orders[$i] = $i;
        $roles[$i]  = 'reader';
        $types[$i]  = 'dept';
        $depts[$i]  = $user->dept_id;

        $request->orders = $orders;
        $request->roles  = $roles;
        $request->types  = $types;
        $request->users  = $users;
        $request->depts  = $depts;
        
        if_debug( $request );
        
        $access_list = AccessListAction::creates( $request );
        
        return $access_list;        
    }

    //  自分の公開カレンダーを生成（自分のWrite権限カレンダーがない場合）
    //  Write権限カレンダーがあれば、Null を返す
    //
    public static function initCalendar( User $user, AccessList $access_list ) {
        
        $calendars = Calendar::getCanWrite( $user );
        if( count( $calendars )) { return null; }
        
        $request = new Request;
        $request->name = $user->name . "のカレンダー";
        $request->memo = "初期自動生成カレンダー";
        // $request->type = 'public';
        $request->type = 'private';
        
        $request->default_permission = 'creator';
        $request->access_list_id = $access_list->id;

        $calendar = CalendarAction::creates( $request );
        
        return $calendar;
    }
    
    //  自分のタスクリストを生成（自分のWrite権限カレンダーがない場合）
    //  Write権限カレンダーがあれば、Null を返す
    //
    //　自分のみで使えるタスクリスト（非公開）を生成
    // 
    public static function initTaskList( User $user, AccessList $access_list ) {
        
        $tasklists = TaskList::getCanWrite( $user );
        if( count( $tasklists )) { return null; }
        
        $request = new Request;
        $request->name = $user->name . "のタスク";
        $request->memo = "初期自動生成タスク（非公開）";
        $request->type = 'private';
        $request->default_permission = 'creator';
        $request->access_list_id = $access_list->id;

        $tasklist = TaskListAction::creates( $request );
        
        return $tasklist;
    }
}

