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
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use App\Http\Helpers\MyGoogleCalendar;

use App\myHttp\GroupWare\Requests\Schedule2Request;
use App\myHttp\GroupWare\Requests\Search\FindSchedulesRequest;;
use App\myHttp\GroupWare\Events\SyncRelatedScheduleToGoogleCalendarEvent;

use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\AccessListUserRole;
use App\myHttp\GroupWare\Models\GCalSync;

use App\myHttp\GroupWare\Events\ScheduleCreatedEvent;
use App\myHttp\GroupWare\Events\ScheduleUpdatedEvent;
use App\myHttp\GroupWare\Events\ScheduleDeletedEvent;
use App\myHttp\GroupWare\Events\ScheduleCalendarHasChangedEvent;

use App\myHttp\GroupWare\Jobs\GsyncScheduleCreatedJob;
use App\myHttp\GroupWare\Jobs\GsyncScheduleUpdatedJob;
use App\myHttp\GroupWare\Jobs\GsyncScheduleUnSyncJob;
use App\myHttp\GroupWare\Jobs\GCalUnSyncWithGcalSyncsJob;

use App\myHttp\GroupWare\Models\Actions\ScheduleAction;
use App\myHttp\GroupWare\Controllers\Search\SearchSchedule;


use App\myHttp\GroupWare\Models\SubClass\ComponentInputFilesClass;
use App\myHttp\GroupWare\Controllers\SubClass\DateTimeInput;

class Schedule2Controller extends Controller {
    
    // 　ルーティングコントローラー
    //
    public function create( Request $request ) {
        $this->authorize( 'create', Schedule::class );

        if( is_null( $request->schedule )) { 
            // 新規作成
            //
            $schedule = new Schedule;
            $schedule->calendar_id = ( $request->calendar_id ) ? $request->calendar_id : '';

            $input    = new DateTimeInput();
            if( isset( $request->start_date ) and isset( $request->end_date )) {
                $input->start_date = $request->start_date;
                $input->end_date   = $request->end_date;
            }
            
            
        } else {
            //　予定の複製
            //
            $schedule = Schedule::find( $request->schedule );
            $request->calendar_id = $schedule->calendar_id;
            $input = new DateTimeInput( $schedule );
            
        }

        if( $request->calendar_id ) {
            $calprop = CalProp::where( 'calendar_id', $request->calendar_id )->where( 'user_id', user_id() )->first();
            $schedule->permission = $calprop->default_permission;
        }
        
        $component_input_files = new ComponentInputFilesClass( 'attach_files'  );
        // $input    = new DateTimeInput( );

        if_debug( $input );
        // return view( 'groupware.schedule2.create' )->with( 'defaults', $defaults );
        BackButton::stackHere( $request );
        return view( 'groupware.schedule2.input' )->with( 'schedule', $schedule )
                                                  ->with( 'input',    $input    )
                                                  ->with( 'component_input_files', $component_input_files );
    }
    
    public function store( Schedule2Request $request ) {

        $this->authorize( 'create', Schedule::class );
        
        $schedule = ScheduleAction::creates( $request );
        
        //  Google カレンダーの同期処理
        //
        GsyncScheduleCreatedJob::dispatch( $schedule );

        //　イベント発布
        //
        // event( new ScheduleCreatedEvent( $schedule ));
        
        // if_debug( 'request->all', $request,  $schedule );
        
        // BackButton::removePreviousSession();
        // session()->flash( 'flash_message', "スケジュール". $request['name']. "を追加しました。" );
        // session()->regenerateToken();
        // return redirect()->route( 'groupware.schedule.show', [ 'schedule' =>  $schedule ]);
        
        return view( 'groupware.schedule2.show' )->with( 'schedule', $schedule );
        
    }
    
    public function edit( Schedule $schedule ) {
        
        $this->authorize( 'update', $schedule );
        
        
        $input    = new DateTimeInput( $schedule );
        if_debug( $input );
        $component_input_files = new ComponentInputFilesClass( 'attach_files', $schedule->files  );
        
        BackButton::stackHere( request() );
        return view( 'groupware.schedule2.input' )->with( 'schedule', $schedule )
                                                  ->with( 'input',    $input    )
                                                  ->with( 'component_input_files', $component_input_files );;
    }
    
    public function update( Schedule $schedule, Schedule2Request $request ) {
        
        $this->authorize( 'update', $schedule );

        $old_schedule = clone $schedule;
        $schedule = ScheduleAction::updates( $schedule, $request );        

        //  Google カレンダーの同期処理
        //
        if( $schedule->calendar_id == $old_schedule->calendar_id ) {
            GsyncScheduleUpdatedJob::dispatch( $schedule );
        } else {
            GsyncScheduleUnSyncJob::dispatch( $old_schedule, $old_schedule->calendar );     // 旧カレンダーの同期解除
            GsyncScheduleCreatedJob::dispatch( $schedule );                                 // 新カレンダーに同期
        }

        // イベント発行
        //
        // event( new ScheduleUpdatedEvent( $schedule ));
        
        session()->regenerateToken();
        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "スケジュール". $request['name']. "を修正しました。" );
        return redirect()->route( 'groupware.schedule.show', [ 'schedule' =>  $schedule ]);
        // return view( 'groupware.schedule2.show' )->with( 'schedule', $schedule );

    }
    
    public function show( Schedule $schedule ) {
        
        $access_list = $schedule->calendar->access_list();
        // if_debug( $access_list->isOwner( user_id() ));
        // if_debug( $access_list->isWriter( user_id() ));
        // if_debug( $access_list->isReader( user_id() ));
        // if_debug( $access_list->CanWrite( user_id() ));
        // if_debug( $access_list->canRead( user_id() ));

        // if_debug( $schedule->calendar );
        $this->authorize( 'view', $schedule );
        
        BackButton::stackHere( request() );
        return view( 'groupware.schedule2.show' )->with( 'schedule', $schedule );
    }
    
    public function delete( Schedule $schedule ) {
        // session()->flash( 'info_message', "スケジュールを削除します。よろしいですか。" );

        $this->authorize( 'delete', $schedule );
        


        BackButton::stackHere( request() );
        return view( 'groupware.schedule2.delete' )->with( 'schedule' , $schedule );
    }
    
    public function deleted( Schedule $schedule ) {

        $this->authorize( 'delete', $schedule );

        //　Google カレンダーを全て削除
        //
        // $gcal_syncs = GCalSync::getBySchedule( $schedule );
        // GCalUnSyncWithGcalSyncsJob::dispatch( $gcal_syncs );
        GsyncScheduleUnSyncJob::dispatch( $schedule, $schedule->calendar );

        // スケジュール削除のイベント
        //
        // event( new ScheduleDeletedEvent( $schedule ));

        $return = ScheduleAction::deletes( $schedule );

        session()->regenerateToken();
        BackButton::removePreviousSession();
        session()->flash( 'info_message', "スケジュールを削除しました" );
        return redirect()->route( 'back_one' );
        // return redirect()->route( 'groupware.schedule.index' );
        // return $this->index( request() );
    }
    
    public function copy( Schedule $schedule ) {
        
        $this->authorize( 'create', Schedule::class );
        $url = route( 'groupware.schedule.create' );
        $url .= '?schedule=' . $schedule->id;
        return redirect( $url );
    
    }


}


