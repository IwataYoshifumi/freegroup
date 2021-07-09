<?php


namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\TaskList;


class TaskListPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) {
        //
        return true;
    }

    public function view(User $user, TaskList $task_list) {
        if( $task_list->canRead( $user->id ) ) {
            return Response::allow();
        }
        return Response::deny( "TaskListPolicy::view 1 : This action is unauthorized. You can not read the task_list" );
    }

    public function create(User $user) {
        if(   $user->is_retired()               ) { return Response::deny( "TaskListPolicy::create 1 : You are retired." ); }
        if( ! $user->hasAccessListsWhoIsOwner() ) { return Response::deny( "TaskListPolicy::create : You do not have AccessLists that you own" ); }
        return true;
    }

    public function update(User $user, TaskList $task_list) {
        
        //　オーナーでなければ変更できない
        //
        if( $task_list->isOwner( $user->id )) { return Response::allow(); }
        
        return Response::deny( "TaskListPolicy::update 1 : This action is unauthorized. You are not TaskList's Owner" );
    }

    public function delete(User $user, TaskList $task_list ) {
        
        //　無効状態でないと削除できない
        //
        if( $task_list->isNotDisabled() ) { return  Response::deny( "TaskListPolicy::delete 2 : The TaskList is available. You Can not delete it." );  }
        
        //　オーナーでなければ削除できない
        //
        if( ! $task_list->isOwner( $user->id )) {
            return Response::deny( "TaskListPolicy::delete 1 : This action is unauthorized. You are not TaskList's Owner" );
        }
        return Response::allow();
    }

}
