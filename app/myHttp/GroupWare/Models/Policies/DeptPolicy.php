<?php


namespace App\myHttp\GroupWare\Models\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;

class DeptPolicy
{
    use HandlesAuthorization;

    // public function __construnct() {
    //     dd( __FILE__, $this );
    // }

    public function viewAny( $auth )
    {
        return Response::allow();
    }


    public function view($auth, Dept $dept)
    {
        return Response::allow();
    }

    public function create( $auth )
    {
        if( auth('admin')->check() ) {
            return Response::allow();
        }
        return Response::deny( 'DeptPolicy@create : error 1');
    }

    public function update($auth, Dept $dept)
    {
        if( auth('admin')->check() ) {
            return Response::allow();
        }
        return Response::deny( 'DeptPolicy@update : No auth');
    }

    //　所属ユーザがいる場合は、削除できない
    //
    public function delete($auth, Dept $dept)
    {
        if( ! auth('admin')->check() ) {
            return Response::deny( 'DeptPolicy@delete : No auth');
        }
        if( $dept->users()->count() >= 1 ) {
            return Response::deny( 'DeptPolicy@delete : Some users still belongs the depertment.');
        } 
        return Response::allow();
    }

    public function restore($auth, Dept $dept)
    {
        //
    }

    public function forceDelete($auth, Dept $dept)
    {
        //
    }
}
