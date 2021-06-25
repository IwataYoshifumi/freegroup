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



use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\Models\Actions\TaskAction;
use App\myHttp\GroupWare\Requests\TaskRequest;
use App\myHttp\GroupWare\Requests\SubRequests\ComfirmDeletionRequest;

use App\myHttp\GroupWare\Models\SubClass\ComponentInputFilesClass;
use App\myHttp\GroupWare\Controllers\SubClass\DateTimeInput;

use App\myHttp\GroupWare\Controllers\Search\SearchTask;


class TaskController extends Controller {

    // 　ルーティングコントローラー
    //
    public function index( Request $request ) {
        
        if_debug( $request->all() );
        
        if( $request->from_menu ) {
            $request->users = [ user_id() ]; 
            $request->calendar_auth = 'reader';
            $request->search_condition = 'users';
            $request->search_date_condition = 'task_date';
            $request->display_axis = 'users';
            $request->status = "未完";
            
            $request->tasklists = TaskList::whereCanWrite( user_id() )->get()->pluck('id')->toArray();
            
        } elseif( $request->tasklist_id ) {
            $request->tasklists = [ $request->tasklist_id ];
            if( ! isset( $request->sorts      )) { $request->sorts = [ 'created_at' ]; }

        } else {
            if( ! $request->tasklists ) {
                $request->tasklists = TaskList::whereCanWrite( user_id() )->get()->pluck('id')->toArray();
            }
            
        }
        
        if( ! isset( $request->pagination )) { $request->pagination = 20; }
            
        //　検索
        //
        $returns = SearchTask::search( $request );
        
        BackButton::setHere( $request );
        return view( 'groupware.task.index' )->with( 'returns', $returns )
                                             ->with( 'request', $request );
    }
    
    public function create( Request $request ) {
        
        $this->authorize( 'create', Task::class );
        
        // if_debug( session()->all() );
        // if_debug( $request->all() );
        
        //　初期値設定
        //
        if( is_null( $request->task )) {
            //　新規タスク作成
            //
            $task = new Task;
            $task->user_id = auth('user')->id();

            $input = new DateTimeInput();
            if( isset( $request->due_date )) {
                $input->due_date = $request->due_date;
            }
        
        } else {
            //
            //　複製
            //
            $task = Task::find( $request->task );
            $task->user_id = user_id();
            
            $input = new DateTimeInput( $task );
            
        }
        
        $component_input_files = new ComponentInputFilesClass( 'attach_files'  );
        
        // if_debug( $task );
        BackButton::stackHere( request() );
        return view( 'groupware.task.input' )->with( 'task', $task )
                                             ->with( 'input', $input )
                                             ->with( 'component_input_files', $component_input_files );
        
    }

    public function store( TaskRequest $request ) {
        
        $this->authorize( 'create', Task::class );
        
        $task = TaskAction::creates( $request );


        session()->regenerateToken();
        session()->flash( 'flash_message', "タスク「". $request->title. "」を追加しました。" );
        BackButton::removePreviousSession();
        
        // return view( 'groupware.task.show' )->with( 'task', $task  );
        return redirect()->route( 'groupware.task.show', [ 'task' =>  $task ]);
        
    }
    
    public function show( Task $task ) {
        // if_debug( $task->schedules );
        $this->authorize( 'view', $task );
        BackButton::stackHere( request() );
        return view( 'groupware.task.show' )->with( 'task', $task );
    }
    
    public function edit( Task $task ) {
        
        $this->authorize( 'update', [ $task, auth('user')->user() ]);
        
        $task->load( 'users','users.dept', 'customers', 'files' );
        $component_input_files = new ComponentInputFilesClass( 'attach_files', $task->files  );
        $input    = new DateTimeInput( $task );
        
        BackButton::stackHere( request() );
        return view( 'groupware.task.input' )->with( 'task', $task )
                                               ->with( 'input',  $input   )
                                               ->with( 'component_input_files', $component_input_files );
    }

    public function update( Task $task, TaskRequest $request ) {
        
        $this->authorize( 'update', [ $task, auth('user')->user() ]);
        
        $task = TaskAction::updates( $task, $request ); 
        
        session()->regenerateToken();
        BackButton::removePreviousSession();

        session()->flash( 'flash_message', "スケジュール". $request['name']. "を修正しました。" );
        return redirect()->route( 'groupware.task.show', [ 'task' =>  $task ]);
        
    }
    
    public function delete( Task $task ) {
        $this->authorize( 'delete', [ $task, auth('user')->user() ]);

        return view( 'groupware.task.delete' )->with( 'task' , $task );
    }
    public function deleted( Task $task, ComfirmDeletionRequest $request ) {
        
        $this->authorize( 'delete', [ $task, auth('user')->user() ]);
        
        TaskAction::deletes( $task );

        session()->regenerateToken();
        return view( 'groupware.task.delete' )->with( 'task' , $task );
    }
    
    public function csv( Request $request ) {
        
        $returns = SearchTask::search( $request );
        
        $tasks = $returns['tasks'];
        // dd( $tasks );
        $values['column_name'] = [ '作成者', '件名', '場所', '開始日', '開始時刻', '終了日', '終了時刻', '所要時間（分）','所要時間（時間）', '関連社員', '関連顧客', '報告内容' ];
        $values['lists'] = [];
        foreach( $tasks as $task ) {
            $attendees = '';
            foreach( $task->users as $attendee ) {
                if( empty( $attendees )) { 
                    $attendees .= $attendee->name;
                } else { 
                    $attendees .= "," . $attendee->name;                    
                }
            }
            // dump( $attendees );
            
            $value = [ 
                op( $task->user )->name,
                $task->name,
                $task->place,
                $task->p_dateTime(),
                $task->memo,
                $attendees
                ];
            array_push( $values['lists'], $value );            
            
        }
        // return response()->json( $values );
        return OutputCSV::input_array( $values );
        
        
    }

    public function copy( Task $task ) {
        
        $this->authorize( 'create', Task::class );
        $url = route( 'groupware.task.create' );
        $url .= '?task=' . $task->id;
        return redirect( $url );
    
    }
    
    // タスクのステータスを完了・未完を切り替える（AJAX）
    //
    public function complete( Task $task  ) {
        
        $this->authorize( 'update', [ $task, auth( 'user' )->user() ]);
        
        $status = DB::transaction( function() use( $task ) {
            if( $task->status == "未完" ) {
                $task->status = "完了";
                $task->completed_time = now();
                $task->user_who_complete = user_id();
            } else {
                $task->status = "未完";
                $task->completed_time = null;
                $task->user_who_complete = null;
            }
            $task->save();
            
            return $task->status;
        });
        
        return response()->json( [ 'id' => $task->id, 'status' => $task->status ]);
        
        
    }
    
    public function showModal( Task $task ) {
        $this->authorize( 'view', $task );
        
        return view( 'groupware.task.show_modal' )->with( 'task', $task );
    }
    


}
