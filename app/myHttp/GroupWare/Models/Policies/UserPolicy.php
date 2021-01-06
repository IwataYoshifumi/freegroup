<?php


namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;


class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny( $auth ) {
        return Response::allow();
    }

    public function view( $auth , User $user) {
        return Response::allow();
    }

    public function create( $auth ) {
        if( auth('admin')->check() ) {
            return Response::allow();
        }
        return Response::deny( 'UserPolicy@create : AuthError 1');
    }

    public function update( $auth, User $user ) {
        if( auth('admin')->check() ) {
            return Response::allow();
        }
        return Response::deny( 'UserPolicy@update : AuthError 1');
    }

    public function delete( $auth, User $user)
    {
        if( auth('admin')->check() ) {
            return Response::allow();
        }
        return Response::deny( 'UserPolicy@create : AuthError 1');
    }

    public function restore( $auth, User $user)
    {
        if( auth('admin')->check() ) {
            return Response::allow();
        }
        return Response::deny( 'UserPolicy@create : AuthError 1');
    }

    public function forceDelete( $auth, User $user)
    {
        if( auth('admin')->check() ) {
            return Response::allow();
        }
        return Response::deny( 'UserPolicy@create : AuthError 1');
    }
}
