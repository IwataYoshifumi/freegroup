<?php


namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\myHttp\GroupWare\Models\Template;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;

class TemplatePolicy
{
    use HandlesAuthorization;

    public function viewAny( $auth ) {
        if_debug( $auth );
        return Response::allow();
    }

    public function view( $auth , Template $temp) {
        if_debug( $auth );
        return Response::allow();
    }

    public function create( $auth ) {
        if( auth('admin')->check() ) {
            return Response::allow();
        }
        return Response::deny( 'TemplatePolicy@create : AuthError 1');
    }

    public function update( $auth, Template $temp) {
        if( auth('admin')->check() ) {
            return Response::allow();
        }
        return Response::deny( 'TemplatePolicy@update : AuthError 1');
    }

    public function delete( $auth, Template $temp)
    {
        if( auth('admin')->check() ) {
            return Response::allow();
        }
        return Response::deny( 'TemplatePolicy@create : AuthError 1');
    }

    public function restore( $auth, Template $temp)
    {
        if( auth('admin')->check() ) {
            return Response::allow();
        }
        return Response::deny( 'TemplatePolicy@create : AuthError 1');
    }

    public function forceDelete( $auth, Template $temp)
    {
        if( auth('admin')->check() ) {
            return Response::allow();
        }
        return Response::deny( 'TemplatePolicy@create : AuthError 1');
    }
}
