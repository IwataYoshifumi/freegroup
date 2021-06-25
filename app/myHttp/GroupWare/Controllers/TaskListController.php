<?php

namespace App\myHttp\GroupWare\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection ;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Arr;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use App\Http\Controllers\Controller;

use App\myHttp\GroupWare\Requests\TaskListRequest;

use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\Actions\TaskListAction;

use App\myHttp\GroupWare\Controllers\Search\SearchTaskList;

class TaskListController extends Controller {
    
    public function index( Request $request ) {

        if_debug( $request->all());

        //　検索初期条件
        //
        $find = ( isset( $request->find )) ? $request->find : [ 'user_id' => user_id(), 'auth' => 'writer' ];

        //　検索条件を入力せずに private なカレンダーは検索できない
        //
        if( ! op($find)['auth'] or ! $find['user_id'] ) {
            $find['type']['private'] = null;
        }
        if( op( $find )['disabled'] ) { $find['not_use'] = 1; }
        
        $tasklists = SearchTaskList::search( $find );
        $tasklists->load( [ 'taskprops' => function( $query ) { $query->where( 'user_id', user_id() ); } ] );
        
        // dd( $tasklists );
        BackButton::setHere( $request );
        return view( 'groupware.tasklist.index' )->with( 'tasklists', $tasklists )
                                                 ->with( 'find',      $find );
    }
    
    public function show( TaskList $tasklist ) {
        
        BackButton::stackHere( request() );
        return view( 'groupware.tasklist.show' )->with( 'tasklist', $tasklist );
    }
    
    public function create() {
        
        $this->authorize( 'create', TaskList::class );

        $tasklist    = new TaskList;
        $access_list = new AccessList;
        
        BackButton::stackHere( request() );
        return view( 'groupware.tasklist.input' )->with( 'tasklist', $tasklist )
                                                 ->with( 'access_list', $access_list );
    }
    
    public function store( TaskListRequest $request ) {

        $this->authorize( 'create', TaskList::class );

        $tasklist = TaskListAction::creates( $request );
        
        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "カレンダー「". $tasklist->name . "」を作成しました。" );
        session()->regenerateToken();
        return redirect()->route( 'groupware.tasklist.show', [ 'tasklist' => $tasklist->id ]);
    }
    
    public function edit( TaskList $tasklist ) {
        
        $this->authorize( 'update', $tasklist );
            
        $access_list = $tasklist->access_list();
        
        BackButton::stackHere( request() );
        return view( 'groupware.tasklist.input' )->with( 'tasklist', $tasklist )
                                                 ->with( 'access_list', $access_list );
    }
    
    public function update( TaskList $tasklist, TaskListRequest $request ) {

        $this->authorize( 'update', $tasklist );
        
        $old_tasklist = clone $tasklist;
        
        $tasklist = TaskListAction::updates( $tasklist, $request );
        if(( $tasklist->disabled != $old_tasklist->disabled ) and $tasklist->disabled ) {
            // Google 同期解除ジョブを発行

        }

        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "カレンダー「". $tasklist->name . "」を修正しました。" );
        session()->regenerateToken();
        return redirect()->route( 'groupware.tasklist.show', [ 'tasklist' => $tasklist->id ]);
    }
    
    public function delete( TaskList $tasklist ) {
        
        $this->authorize( 'delete', $tasklist );

        BackButton::stackHere( request() );
        return view( 'groupware.tasklist.delete' )->with( 'tasklist', $tasklist );

    }
    
    public function deleted( TaskList $tasklist, TaskListRequest $request ) {
        
        $this->authorize( 'delete', $tasklist );

        TaskListAction::deletes( $tasklist );

        BackButton::removePreviousSession();
        
        session()->regenerateToken();
        session()->flash( 'flash_message', "カレンダー「". $tasklist->name . "」と関連スケジュール等は完全に削除されました" );

        return self::index( $request );
        
        return redirect()->route( 'groupware.tasklist.list' );
    }
    

    
    
    
}
