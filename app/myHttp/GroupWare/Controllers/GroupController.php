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

use App\myHttp\GroupWare\Requests\GroupRequest;
use App\myHttp\GroupWare\Requests\ComfirmDeleteRequest;
use App\myHttp\GroupWare\Models\Actions\GroupAction;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\ACL;

use App\myHttp\GroupWare\Controllers\Search\SearchGroup;


class GroupController extends Controller {

    public function index( Request $request ) {
        // if_debug( $request->input() );
        
        if( ! op( $request )->find and ! $request->users ) {
            $find['access_list']['role']['owner'] ="owner";
            $find['access_list']['user_id']       = auth( 'user' )->id();
        } else {
            $find = $request->find;
        }
        $find['users'] = ( is_array( $request->users )) ? $request->users : [] ;
        
        $groups = SearchGroup::search( $find );
        BackButton::setHere( request() );
        return view( 'groupware.group.index')->with( 'groups', $groups )
                                             ->with( 'find', $find     );
    }

    public function show( Group $group ) {
        
        BackButton::stackHere( request() );
        return view( 'groupware.group.show' )->with( 'group', $group );
    }
    
    public function create() {

        $group = new Group;
        $users = [];
        
        BackButton::stackHere( request() );
        return view( 'groupware.group.input' )->with( 'group', $group )
                                              ->with( 'users', $users );
    }
    
    public function store( GroupRequest $request ) {
        
        $group = GroupAction::creates( $request );
        $users = $request->users;

        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "グループ「". $group->name . "」を追加しました。" );
        session()->regenerateToken();
        return redirect()->route( 'groupware.group.show', [ 'group' => $group->id ]);
        // return view( 'groupware.group.input' )->with( 'group', $group )
        //                                       ->with( 'users', $users );
    }

    public function edit( Group $group ) {
        $this->authorize('update', $group );
        
        $users = toArrayKeyIncremental( $group->users, 'id' );
        
        BackButton::stackHere( request() );
        return view( 'groupware.group.input' )->with( 'group', $group )
                                              ->with( 'users', $users );
    }
    
    public function update( GroupRequest $request, Group $group ) {
        $this->authorize('update', $group );
        
        $group = GroupAction::updates( $group, $request );
        
        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "グループ「". $group->name . "」を修正しました。" );
        session()->regenerateToken();
        return redirect()->route( 'groupware.group.show', [ 'group' => $group->id ]);

        // $users = $request->users;
        // return view( 'groupware.group.input' )->with( 'group', $group )
        //                                       ->with( 'users', $users );
        
        
    }
    
    // kaihatsu yotei  GroupController@delete 将来、割当済みGroupは、割当を全て解除しないと削除できないようにする
    public function delete( Group $group ) {
        
        $this->authorize('delete', $group );
        
        BackButton::stackHere( request() );
        return view( 'groupware.group.delete' )->with( 'group', $group );
    }

    public function deleted( ComfirmDeleteRequest $request, Group $group ) {
        
        $this->authorize('delete', $group );
        
        GroupAction::deletes( $group );
        
        
        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "グループ「". $group->name . "」を削除しました。" );
        session()->regenerateToken();
        return redirect()->route( 'groupware.group.index' );
    }
    
}
