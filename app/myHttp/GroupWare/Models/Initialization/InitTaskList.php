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

class InitTaskList  {
    
    // protected $table = 'groups';
    
    //　新規ユーザ作成などの時 or ログイン時にコールされる
    //  ユーザに対応するTaskPropが存在しない場合は生成する 
    //
    public static function forUser( $user ) {
        $user = ( $user instanceof User ) ? $user : User::find( $user->id );
        
        $tasklists = TaskList::whereDoesntHave( 'taskprops', function( $query ) use ( $user ) {
                    $query->where( 'user_id', $user->id );
                })->get();
        
        foreach( $tasklists as $tasklist ) {
            self::init( $tasklist, [ $user ] );
        }

    }
    
    //　新規にTaskList作成した後で呼び出される
    //  全ユーザ分のTaskPropを生成する
    //
    public static function withTaskList( TaskList $tasklist ) {
        $users = User::all();
        return self::init( $tasklist, $users );
    }
    
    //　TaskPropを生成
    //
    private static function init( TaskList $tasklist, $users ) {
// dd( $tasklist );
        $values  = [ 'task_list_id'     => $tasklist->id, 
                     'name'               => $tasklist->name,
                     'memo'               => $tasklist->memo,
                     'text_color'         => TaskProp::default_text_color(),
                     'background_color'   => TaskProp::default_background_color(),
                     'default_permission' => $tasklist->default_permission,
                ];
        
        $taskprops = []; 
        foreach( $users as $user ) {
            
            $values['user_id'] = $user->id;
                // dd( $values);
            $taskprop = TaskProp::create( $values );
            $taskprops[ $taskprop->id ] = $taskprop;
        }
        return $taskprops;
    }

}

