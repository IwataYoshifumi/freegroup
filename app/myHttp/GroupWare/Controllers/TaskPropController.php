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
use Exception;
use Illuminate\Auth\Access\Response;

use App\Http\Controllers\Controller;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\myHttp\GroupWare\Requests\TaskPropRequest;
use App\myHttp\GroupWare\Models\Actions\TaskPropAction;

use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\File as MyFile;


class TaskPropController extends Controller {
    
    public function index( Request $request ) {
        $controller = new TaskListController;
        return $controller->index( $request );
    }
    
    public function show( TaskProp $taskprop ) {

        $this->authorize( 'view', $taskprop );
        if_debug( $taskprop->getAttributes() );

        BackButton::stackHere( request() );
        return view( 'groupware.taskprop.show' )->with( 'taskprop', $taskprop );
    }
    
    public function create() {
        $this->authorize( 'create', TaskProp::class );
    }
    
    public function store( TaskPropRequest $request ) {
        $this->authorize( 'create', TaskProp::class );
    }
    
    public function edit( TaskProp $taskprop ) {

        if_debug( old() );
        $this->authorize( 'update', $taskprop );
        
        BackButton::stackHere( request() );
        return view( 'groupware.taskprop.input' )->with( 'taskprop', $taskprop );
    }
    
    public function update( TaskProp $taskprop, TaskPropRequest $request ) {

        $this->authorize( 'update', $taskprop );

        $old_taskprop = clone $taskprop;
        $taskprop = TaskPropAction::updates( $taskprop, $request );        

        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "タスク表示設定を変更しました。" );
        session()->regenerateToken();
        return redirect()->route( 'groupware.taskprop.show', [ 'taskprop' => $taskprop->id ]);

        // return view( 'groupware.taskprop.show' )->with( 'taskprop', $taskprop );
    }
    
    public function delete() {
        return die( __METHOD__ );
    }
    
}
