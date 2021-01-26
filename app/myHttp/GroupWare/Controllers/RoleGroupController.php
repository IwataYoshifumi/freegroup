<?php

namespace App\myHttp\GroupWare\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use DB;

use App\Http\Controllers\Controller;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\myHttp\GroupWare\Requests\RoleGroupRequest;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\RoleGroup;
use App\myHttp\GroupWare\Models\RoleList;
use App\myHttp\GroupWare\Models\Actions\RoleGroupAction;


class RoleGroupController extends Controller {

    public function index( Request $request ) {

        $find = ( isset( $request->find )) ? $request->find : [];
        // $show = [];
        
        $role_groups = RoleGroupAction::search( $find );
        // $role_groups = RoleGroup::all();

        BackButton::setHere( $request );
        return view( 'groupware.rolegroup.index' )->with( 'role_groups', $role_groups )
                                                  ->with( 'find', $find );
    }

    public function show( RoleGroup $role_group ) {

        BackButton::stackHere( request() );
        return view( 'groupware.rolegroup.show' )->with( 'role_group', $role_group );
    }
    
    public function create() {

        $lists = [];        
        $role_group = new RoleGroup;
        
        BackButton::stackHere( request() );
        // return view( 'groupware.rolegroup.create' )->with( 'lists', $lists );
        return view( 'groupware.rolegroup.input' )->with( 'lists', $lists )
                                                  ->with( 'role_group', $role_group );
    }
    
    public function store( RoleGroupRequest $request ) {

        $role_group = RoleGroup::creates( $request );
        
        session()->regenerateToken();
        session()->flash( 'flash_message', "ロールグループ「". $request->name. "」を追加しました。" );
        BackButton::removePreviousSession();
        return redirect()->route( 'groupware.role_group.show', [ 'role_group' => $role_group ]);
        
    }

    public function edit( RoleGroup $role_group ) {
        
        $lists = [];
        // if_debug( $role_group );
        foreach( $role_group->role_lists as $list ) {
            array_push( $lists, $list->role );
        }
        
        BackButton::stackHere( request() );
        return view( 'groupware.rolegroup.input' )->with( 'role_group', $role_group )
                                                  ->with( 'lists', $lists );
    }
    
    public function update( RoleGroup $role_group, RoleGroupRequest $request ) {

        $role_group->updates( $request );
    
        // if_debug( $role_group);
    
        session()->regenerateToken();
        session()->flash( 'flash_message', "ロールグループ「". $request->name. "」を修正しました。" );
        BackButton::removePreviousSession();
        // return view( 'groupware.rolegroup.show' )->with( 'role_group', $role_group );
        return redirect()->route( 'groupware.role_group.show', [ 'role_group' => $role_group ]);
    }
    
    public function delete( RoleGroup $role_group ) {

        BackButton::stackHere( request() );
        
        if( $role_group->default ) {
            session()->flash( 'error_message', "デフォルトのロールグループは削除できません。" );
            return redirect()->route( 'groupware.role_group.show', [ 'role_group' => $role_group ] );
        }
        
        return view( 'groupware.rolegroup.show' )->with( 'role_group', $role_group );
        
    }

    public function deleted( RoleGroup $role_group, RoleGroupRequest $request ) {

        $role_group->deletes();
        
        session()->regenerateToken();
        session()->flash( 'flash_message', "ロールグループ「". $role_group->name. "」を削除しました。" );
        
        BackButton::removePreviousSession();
        // return view( 'groupware.rolegroup.show' )->with( 'role_group', $role_group );
        return redirect()->route( 'groupware.role_group.index' );
    }

    public function select_users( Request $request ) {
        
        // if_debug( $request );
        $find = ( isset( $request->find )) ? $request->find : [ 'pagination' => 10 ];
        $show = [];
        
        $users = RoleGroupAction::search_users( $find );
        // dd( $users );
        // $role_groups = RoleGroup::all();

        BackButton::stackHere( $request );
        return view( 'groupware.rolegroup.select_users' )->with( 'users', $users )
                                                         ->with( 'find', $find );
    }
    
    public function select_role( Request $request ) {
        
        BackButton::stackHere( $request );
        return view( 'groupware.rolegroup.attach_role' );
        
    }

    public function attach_role( RoleGroupRequest $request ) {

        try {
            $role_group = RoleGroupAction::attach_users( $request );
        } catch( Exception $e ) {
           report( $e ); 
        }
        
        session()->regenerateToken();
        session()->flash( 'flash_message', "ロールを割当しあした。" );
        BackButton::removePreviousSession();
        return view( 'groupware.rolegroup.show' )->with( 'role_group', $role_group );
    }

    
}
