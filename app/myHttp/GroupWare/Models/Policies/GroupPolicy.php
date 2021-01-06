<?php


namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\ACL;


class GroupPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        //
    }

    public function view(User $user, Group $group)
    {
        //
    }

    public function create(User $user)
    {
        //
    }

    public function update(User $user, Group $group)
    {
        //　オーナーでなければ変更できない
        //
        if( ! $group->access_list()->isOwner( $user->id )) {
            return Response::deny( "GroupPolicy::update 1 : This action is unauthorized. You are not Group's Owner" );
        }
        return Response::allow();
    }

    public function delete(User $user, Group $group)
    {
        // アクセスリストに設定していなければ削除可能
        //
        if( ACL::whereGroup( $group->id )->count() >= 1 ) {
            return Response::deny( "GroupPolicy::delete 1 : This action is unauthorized. The Group is attached access lists" );
        }
        //　オーナーでなければ削除できない
        //
        if( ! $group->access_list()->isOwner( $user->id )) {
            return Response::deny( "GroupPolicy::delete 2 : This action is unauthorized. You are not Group's Owner" );
        }
        return Response::allow();
    }

    public function restore(User $user, Group $group)
    {
        //
    }

    public function forceDelete(User $user, Group $group)
    {
        //
    }
}
