<?php


namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;


class TaskPropPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) {
        //
    }
    public function view( User $user, TaskProp $taskprop) {
        if( $taskprop->user_id != $user->id ) {
            return Response::deny( "TaskPropPolicy::view 1 : This action is unauthorized. You are not TaskProp's Owner" );
        }
        return Response::allow();
    }

    //　自動生成のため create アクションはなし
    //
    public function create(User $user) {
        return Response::deny( "TaskPropPolicy::create 1 : This action is unauthorized." );
    }

    public function update(User $user, TaskProp $taskprop) {
        
        if( $taskprop->tasklist->is_disabled() ) {
            return Response::deny( "TaskPropPolicy::update 2 : TaskList has been disabled." );
        }
        
        if( $taskprop->user_id != $user->id ) {
            return Response::deny( "TaskPropPolicy::update 1 : This action is unauthorized. You are not TaskProp's Owner" );
        }
        return Response::allow();
    }

    public function delete(User $user, TaskProp $taskprop) {
        die( __METHOD__ );
    }
    public function restore(User $user, TaskProp $taskprop) {
        die( __METHOD__ );
    }
    public function forceDelete(User $user, TaskProp $taskprop) {
        die( __METHOD__ );
    }
}
