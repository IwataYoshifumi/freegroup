<?php


namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\AccessList;

class AccessListPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\myHttp\GroupWare\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\myHttp\GroupWare\Models\User  $user
     * @param  \App\AccessList  $access_list
     * @return mixed
     */
    public function view(User $user, AccessList $access_list)
    {
        //
        if( $access_list->canRead( $user )) {
            return Response::allow();
        }
        return Response::deny( __METHOD__ );
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\myHttp\AccessListWare\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\myHttp\GroupWare\Models\User  $user
     * @param  \App\AccessList  $access_list
     * @return mixed
     */
    public function update(User $user, AccessList $access_list)
    {
        //　オーナーでなければ変更できない
        //
        if( ! $access_list->isOwner( $user->id )) {
            return Response::deny( "AccessListPolicy::update 1 : This action is unauthorized. You are not AccessList's Owner" );
        }
        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\myHttp\GroupWare\Models\User  $user
     * @param  \App\AccessList  $access_list
     * @return mixed
     */
    public function delete(User $user, AccessList $access_list)
    {
        
        // if_debug( $access_list->isAttached(), $access_list->groups, $access_list->calendars, $access_list->files );
        //
        // アクセスリストが使われていなければ、削除可能
         if( $access_list->isAttached() ) {
        
            return Response::deny( "AccessListPolicy::delete 1 : This action is unauthorized. AccessList is attached with something " );
        }
        
        //　オーナーでなければ削除できない
        //
        if( ! $access_list->isOwner( $user->id )) {
            return Response::deny( "AccessListPolicy::delete 2 : This action is unauthorized. You are not AccessList's Owner" );
        }
        return Response::allow();
    }


    public function restore(User $user, AccessList $access_list)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\myHttp\GroupWare\Models\User  $user
     * @param  \App\AccessList  $access_list
     * @return mixed
     */
    public function forceDelete(User $user, AccessList $access_list)
    {
        //
    }
}
