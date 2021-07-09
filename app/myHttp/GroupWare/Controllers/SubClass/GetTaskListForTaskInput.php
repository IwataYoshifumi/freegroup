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
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;

use App\myHttp\GroupWare\Requests\AccessListRequest;
use App\myHttp\GroupWare\Requests\DeleteAccessListRequest;

class GetTaskListForTaskInput {

    //　タスク入力フォームで書き込み可能なタスクリストを検索する ( view の schedule.input で利用 )
    //
    public static function user( $user_id ) {
        
        $subquery_1 = TaskProp::select( 'task_list_id' )->where( 'user_id', $user_id )->where( 'not_use', '!=', 1 );
        $writables  = TaskList::getCanWrite( $user_id )->pluck('id')->toArray(); 
        $query      = TaskList::where( 'not_use', '!=', 1 )->where( 'disabled', '!=', 1 )->whereIn( 'id', $subquery_1 )->whereIn( 'id', $writables );
        $task_lists  = $query->get();
        
        // if_debug( $task_lists, $query );
        
        return $task_lists;
    }
    
    public static function getFromUserAndTaskList( $user_id, TaskList $task_list ) {
        $task_lists = self::user( $user_id );
        $task_lists->push( $task_list );

        return $task_lists;
        
    }

    
}
