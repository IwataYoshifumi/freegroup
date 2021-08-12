<?php

namespace App\myHttp\GroupWare\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use DB;
use Carbon\Carbon;

use App\Http\Controllers\Controller;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;

use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;

use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\AccessListUserRole;

use App\myHttp\GroupWare\Controllers\Search\SearchSchedulesAndTasks;
use App\myHttp\GroupWare\Controllers\Search\SearchForShowALLIndex;

use App\myHttp\GroupWare\Requests\ShowAllIndexRequest;

class ShowAllController extends Controller {

    public function monthly( Request $request ) {

        //　検索初期条件の設定
        //
        // dump( $request->all() );
        
        $today = new Carbon( 'today' );
        if( ! isset( $request->base_date )) { 
            $request->base_date =  $today->format( 'Y-m-d' );
            $request->show_hidden_calendars = 0;
            $request->show_hidden_tasklists = 0;
            $request->calendar_permission = "writer";
            $request->tasklist_permission = "writer";
            $request->task_status = '未完';

            // $request->calendars = Calendar::getCanWrite( user_id() )->pluck('id')->toArray();
            // $request->tasklists = TaskList::getCanWrite( user_id() )->pluck('id')->toArray();
            $request->calendars = SearchSchedulesAndTasks::searchCalendars( $request )->pluck('id')->toArray();
            $request->tasklists = SearchSchedulesAndTasks::searchTaskLists( $request )->pluck('id')->toArray();
        }
        if( ! isset( $request->span )) {
            $request->span = "monthly";
        }
        
        //　スケジュールとタスクを件枠
        //
        $returns = SearchSchedulesAndTasks::search( $request );
        // dump( $returns );
        BackButton::setHere( $request );
        return view( 'groupware.show_all.monthly' )->with( 'request', $request )
                                                   ->with( 'returns', $returns );
    }
    
    public function weekly( Request $request ) {

        //　検索初期条件の設定
        //
        // dump( $request->all() );
        
        $today = new Carbon( 'today' );
        if( ! isset( $request->base_date )) { 
            $request->base_date =  $today->format( 'Y-m-d' );
            $request->show_hidden_calendars = 0;
            $request->show_hidden_tasklists = 0;
            $request->calendar_permission = "writer";
            $request->tasklist_permission = "writer";
            $request->task_status = '未完';

            // $request->calendars = Calendar::getCanWrite( user_id() )->pluck('id')->toArray();
            // $request->tasklists = TaskList::getCanWrite( user_id() )->pluck('id')->toArray();
            $request->calendars = SearchSchedulesAndTasks::searchCalendars( $request )->pluck('id')->toArray();
            $request->tasklists = SearchSchedulesAndTasks::searchTaskLists( $request )->pluck('id')->toArray();
        }
        if( ! isset( $request->span )) {
            $request->span = "weekly";
        }
        
        //　スケジュールとタスクを件枠
        //
        $returns = SearchSchedulesAndTasks::search( $request );
        // dump( $returns );
        BackButton::setHere( $request );
        return view( 'groupware.show_all.weekly' )->with( 'request', $request )
                                                  ->with( 'returns', $returns );
        
    }
    
    public function daily( Request $request ) {

        //　検索初期条件の設定
        //
        // dump( $request->all() );
        
        $today = new Carbon( 'today' );
        if( ! isset( $request->base_date )) { 
            $request->base_date =  $today->format( 'Y-m-d' );
            $request->calendars = Calendar::getCanWrite( user_id() )->pluck('id')->toArray();
            $request->tasklists = TaskList::getCanWrite( user_id() )->pluck('id')->toArray();

            $request->show_hidden_calendars = 0;
            $request->show_hidden_tasklists = 0;
            $request->calendar_permission = "writer";
            $request->tasklist_permission = "writer";
            $request->task_status = '未完';

        }
        if( ! isset( $request->span )) {
            $request->span = "daily";
        }
        
        //　スケジュールとタスクを件枠
        //
        $returns = SearchSchedulesAndTasks::search( $request );
        
        BackButton::stackHere( $request );
        return view( 'groupware.show_all.daily' )->with( 'request', $request )
                                                   ->with( 'returns', $returns );
        
    }
    
    public function index( Request $request ) {

        if_debug( $request->all(), old() );

        //　検索初期条件の設定
        //
        if((! count( $request->all() ) and ! count( old()) ) or $request->set_defaults ) { 
            $request->show_hidden_calendars = 0;
            $request->show_hidden_tasklists = 0;
            $request->calendar_permission = "writer";
            $request->tasklist_permission = "writer";
            $request->task_status = '未完';
            $request->users = [ user_id() ];
            $request->calendars = Calendar::getCanWrite( user_id() )->pluck('id')->toArray();
            $request->tasklists = [];
            $request->report_lists = [];
        }
        if( $request->writable_calender ) {
            $request->calendars = Calendar::getCanWrite( user_id() )->pluck('id')->toArray();
            $request->tasklists = [];
            $request->report_lists = [];
            
        } elseif( $request->writable_tasklist ) {
            $request->calendars = [];
            $request->report_lists = [];
            $request->tasklists = TaskList::getCanWrite( user_id() )->pluck('id')->toArray();
            $request->task_status = '未完';
        } elseif( $request->writable_report_list ) {
            $request->calendars = [];
            $request->report_lists = ReportList::getCanWrite( user_id() )->pluck('id')->toArray();
            $request->tasklists = [];
            $request->task_status = '未完';
        }
        if( empty( $request->order_by )) { $request->order_by = [ 'time' ]; }
        
        if( ! $request->pegination ) { $request->pagination = 30; }

        //　スケジュールとタスクを件枠
        //
        BackButton::setHere( $request );
        return view( 'groupware.show_all.index' )->with( 'request', $request )
                                                 ->with( 'returns', [] );
    }
    
    
    
    public function indexExecSearch( ShowAllIndexRequest $request ) {

        if( empty( $request->order_by )) { $request->order_by = [ 'time' ]; }
        if( ! $request->pegination ) { $request->pagination = 30; }

        //　スケジュール・タスク・（報告書）の検索
        //
        $returns = SearchForShowALLIndex::search( $request );

        BackButton::setHere( $request );
        return view( 'groupware.show_all.index' )->with( 'request', $request )
                                                 ->with( 'returns', $returns );
    }
    
    
    public function dailyDiallog( Request $request ) {

        $today = new Carbon( 'today' );
        if( ! isset( $request->base_date )) { 
            $request->base_date =  $today->format( 'Y-m-d' );
            $request->calendars = Calendar::getCanWrite( user_id() )->pluck('id')->toArray();
            $request->tasklists = TaskList::getCanWrite( user_id() )->pluck('id')->toArray();

            $request->show_hidden_calendars = 0;
            $request->show_hidden_tasklists = 0;
            $request->calendar_permission = "writer";
            $request->tasklist_permission = "writer";
            $request->task_status = '未完';

        }
        if( ! isset( $request->span )) {
            $request->span = "daily";
        }
        
        //　スケジュールとタスクを件枠
        //
        $returns = SearchSchedulesAndTasks::search( $request );
        
        BackButton::stackHere( $request );
        return view( 'groupware.show_all.daily_body' )->with( 'request', $request )
                                                      ->with( 'returns', $returns );
    }

}
