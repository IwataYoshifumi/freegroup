<?php


namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\ACL;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;


class CalPropPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user) {
        //
    }
    public function view(User $user, CalProp $calprop) {
        if( $calprop->user_id != $user->id ) {
            return Response::deny( "CalPropPolicy::view 1 : This action is unauthorized. You are not CalProp's Owner" );
        }
        return Response::allow();
    }

    //　自動生成のため create アクションはなし
    //
    public function create(User $user) {
        return Response::deny( "CalPropPolicy::create 1 : This action is unauthorized." );
    }

    public function update(User $user, CalProp $calprop) {
        if( $calprop->user_id != $user->id ) {
            dd( $user->id, $calprop->user_id, $calprop );
            return Response::deny( "CalPropPolicy::update 1 : This action is unauthorized. You are not CalProp's Owner" );
        }
        return Response::allow();
    }

    //　削除・リストアはない
    //
    public function delete(User $user, CalProp $calprop) {
        return Response::deny( "CalPropPolicy::create 1 : This action is unauthorized." );
    }
    public function restore(User $user, CalProp $calprop) {
        return false;
    }
    public function forceDelete(User $user, CalProp $calprop) {
        return false;
    }
}
