<?php

namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\TaskList;


class TaskPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) {
        return TRUE;
    }

    public function view(User $user, Task $task) {

        if( $task->canRead( $user )) { return Response::allow(); }        

        return Response::deny( 'TaskPolicy@view : deny 1 ');
    }

    public function create(User $user) {
        
        //　タスク作成可能なタスクリストがあるか確認
        //
        $tasklists = TaskList::whereCanWrite( $user )->where( 'not_use', false )->get();
        
        if( count( $tasklists ) >= 1 ) { 
            return true; 
            
        } else {
            // タスクを作成できるタスクリストがない
            //
            $message = "新規にタスクを登録できるタスクリストがありません。タスクリスト管理者に連絡して作成権限を与えてもらうか、新規にタスクリストを作成してください";
            session()->flash( $message );
            return Response::deny( $message );
        } 
    }

    public function update(User $user, Task $task) {

        $tasklist = $task->tasklist;
        
        if( $tasklist->is_disabled() ) {
            return Response::deny( 'TaskPolicy@update 2 : The TaskList has been Disabled' );
        }
        
        if( $user->id == $task->user_id ) {
            return Response::allow();
        } 
        if( $task->permission == "creator" and $user->id != $task->user_id ) {
            return Response::deny( 'TaskPolicy@update 1 : you are not creator' );
        }
        if( $task->permission == "attendees" and $task->isAttendee( $user )) {
            return Response::allow();
        }

        if( $task->permission == "writers" ) {
            $access_list = $task->tasklist->access_list();
            if( $access_list->canWrite( $user->id ) ) {
                return Response::allow();
            }
            
        }
        return Response::deny( 'TaskPolicy@update : deny at all');
    }

    public function delete(User $user, Task $task) {

        
        // return $user->id == $task->user_id;
        return $this->update( $user, $task );
    }

}
