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

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\Actions\AccessListAction;
use App\myHttp\GroupWare\Models\ACL;

use App\myHttp\GroupWare\Requests\AccessListRequest;
use App\myHttp\GroupWare\Requests\DeleteAccessListRequest;
use App\myHttp\GroupWare\Controllers\Search\SearchAccessList;
use App\myHttp\GroupWare\Models\Search\CheckAccessList;


class AccessListController extends Controller {

    public function index( Request $request ) {
        
        $find = ( isset( $request->find )) ? $request->find : [];
        $show = [];
        if( empty( $find['role'] )) { $find['role']['owner'] = "owner"; }


        if( ! optional( $find )['user_id'] ) { $find['user_id'] = auth( 'user' )->id(); }


        $access_lists = SearchAccessList::search( $find );
        
        // $access_lists = AccessList::all();
        
        BackButton::setHere( $request );
        return view( 'groupware.access_list.index')->with( 'access_lists', $access_lists )
                                                   ->with( 'find', $find );
    }

    public function show( AccessList $access_list ) {
        
        BackButton::stackHere( request() );
        return view( 'groupware.access_list.show' )->with( 'access_list', $access_list );
        
    }
    
    public function create() {
        $access_list = new AccessList;
        
        $roles = [];
        $users = [];
        $depts = [];
        $groups= [];
        $types = [];
        $orders= [ 1,2,3,4,5,6 ];

        BackButton::stackHere( request() );
        return view( 'groupware.access_list.input' )->with( 'access_list', $access_list )
                                                    ->with( 'orders', $orders )
                                                    ->with( 'roles', $roles )
                                                    ->with( 'depts', $depts )
                                                    ->with( 'groups', $groups )
                                                    ->with( 'types', $types )
                                                    ->with( 'users', $users );
    }
    
    public function store( AccessListRequest $request ) {
        
        $access_list = AccessListAction::creates( $request );

        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "アクセスリスト「". $access_list->name . "」を作成しました。" );
        session()->regenerateToken();
        return redirect()->route( 'groupware.access_list.show', [ 'access_list' => $access_list->id ]);
        // return view( 'groupware.access_list.input' )->with( 'access_list', $access_list )
        //                                             ->with( 'roles', $roles )
        //                                             ->with( 'depts', $depts )
        //                                             ->with( 'groups', $groups )
        //                                             ->with( 'types', $types )
        //                                             ->with( 'users', $users );
        
        
        
    }

    public function edit( AccessList $access_list ) {
        
        $this->authorize( 'update', $access_list );
        
        // if_debug( request()->input(), old(), old("users[]") );

        $array = $access_list->get_arrays_for_selector();
        // if_debug( $array );
        $orders= $array['orders'];
        $roles = $array['roles'];
        $users = $array['users'];
        $depts = $array['depts'];
        $groups= $array['groups'];
        $types = $array['types'];
        
        BackButton::stackHere( request() );
        return view( 'groupware.access_list.input' )->with( 'access_list', $access_list )
                                                    ->with( 'orders', $orders )
                                                    ->with( 'roles', $roles )
                                                    ->with( 'depts', $depts )
                                                    ->with( 'groups', $groups )
                                                    ->with( 'types', $types )
                                                    ->with( 'users', $users );
    }
    
    public function update( AccessListRequest $request, AccessList $access_list ) {
        $this->authorize( 'update', $access_list );
        
        $access_list = AccessListAction::updates( $access_list, $request );

        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "アクセスリスト「". $access_list->name . "」を修正しました。" );
        session()->regenerateToken();
        return redirect()->route( 'groupware.access_list.show', [ 'access_list' => $access_list->id ]);
        
    }
    
    public function delete( AccessList $access_list ) {  // kaihatsu yotei
        $this->authorize( 'delete', $access_list );
    
    
        BackButton::stackHere( request() );
        return view( 'groupware.access_list.delete' )->with( 'access_list', $access_list );
    }

    public function deleted( AccessList $access_list, DeleteAccessListRequest $request ) {  // kaihatsu yotei
        $this->authorize( 'delete', $access_list );

    
        AccessListAction::deletes( $access_list );
        
        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "グループ「". $access_list->name . "」を削除しました。" );
        session()->regenerateToken();
        return redirect()->route( 'groupware.access_list.index' );
    }
    

    
}
