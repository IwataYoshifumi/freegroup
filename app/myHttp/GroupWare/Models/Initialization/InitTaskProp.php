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
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;

use App\myHttp\GroupWare\Models\Actions\AccessListUserRoleUpdate;

class InitTaskProp  {
    
    // protected $table = 'groups';
    
    //　新規ユーザ作成などの時 or ログイン時にコールされる
    //  ユーザに対応するTaskPropが存在しない場合は生成する 
    //
    public static function forUser( $user ) {
        $user = ( $user instanceof User ) ? $user : User::find( $user->id );
        
        $task_lists = TaskList::whereDoesntHave( 'task_props', function( $query ) use ( $user ) {
                    $query->where( 'user_id', $user->id );
                })->get();
        
        foreach( $task_lists as $task_list ) {
            self::init( $task_list, [ $user ] );
        }

    }
    
    //　新規にTaskList作成した後で呼び出される
    //  全ユーザ分のTaskPropを生成する
    //
    public static function withTaskList( TaskList $task_list ) {
        $users = User::all();
        return self::init( $task_list, $users );
    }
    
    //　TaskPropを精製
    //
    private static function init( TaskList $task_list, $users ) {

        $values  = [ 'task_list_id'     => $task_list->id, 
                     'name'               => $task_list->name,
                     'memo'               => $task_list->memo,
                     'text_color'         => TaskProp::default_text_color(),
                     'background_color'   => TaskProp::default_background_color(),
                     'default_permission' => $task_list->default_permission,
                ];
        
        $task_props = []; 
        foreach( $users as $user ) {
            $values['user_id'] = $user->id;
            $task_prop = TaskProp::create( $values );
            $task_props[ $task_prop->id ] = $task_prop;
        }
        return $task_props;
    }

}

