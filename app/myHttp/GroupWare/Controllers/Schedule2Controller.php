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
use App\myHttp\GroupWare\Controllers\Search\GetCalendarForScheduleInput;
use App\myHttp\GroupWare\Controllers\Search\SearchSchedule;

use App\myHttp\GroupWare\Models\SubClass\ComponentInputFilesClass;

class Schedule2Controller extends Controller {
    
    // 　ルーティングコントローラー
    //
    public function index( Request $request ) {
        
        GetCalendarForScheduleInput::user( user_id() );
        
        $find = ( isset( $request->find )) ? $request->find : [];
        $schedules = Schedule::paginate( 20 );
        
        BackButton::setHere( $request );
        return view( 'groupware.schedule2.index' )->with( 'schedules', $schedules )
                                                  ->with( 'find', $find );
        
    }
    
    public function monthly( Request $request ) {
        $base_date = ( empty( $request->base_date )) ? Carbon::today() : new Carbon( $request->base_date );
        $schedules = SearchSchedule::search( $request );
        // dump( $schedules->find(138));
        
        //　表示する月カレンダーデータ取得
        //
        $dates = getMonthlyCalendarDates( $base_date );
        $schedule_ids = get_array_dates_schedule_id( $schedules );

        BackButton::setHere( $request );
        return view( 'groupware.schedule2.monthly')->with( 'request' , $request )
                                                   ->with( 'dates', $dates )
                                                   ->with( 'base_date', $base_date )
                                                   ->with( 'schedule_ids', $schedule_ids )
                                                   ->with( 'schedules', $schedules );
    }

    public function weekly( Request $request ) {
    }

    public function daily( Request $request ) {
    }
    
    public function create( Request $request ) {
        $this->authorize( 'create', Schedule::class );
        
        // dump( old() );
        $schedule = new Schedule;
        $schedule->calendar_id = ( $request->calendar_id ) ? $request->calendar_id : '';

        if( $request->calendar_id ) {
            $calprop = CalProp::where( 'calendar_id', $request->calendar_id )->where( 'user_id', user_id() )->first();
            $schedule->permission = $calprop->default_permission;
        }
        
        $component_input_files = new ComponentInputFilesClass( 'attach_files'  );
        
        
        $input    = new DateTimeInput( );

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
        
        // dump( 'request->all', $request,  $schedule );
        
        // BackButton::removePreviousSession();
        // session()->flash( 'flash_message', "スケジュール". $request['name']. "を追加しました。" );
        // session()->regenerateToken();
        // return redirect()->route( 'groupware.schedule.show', [ 'schedule' =>  $schedule ]);
        
        return view( 'groupware.schedule2.show' )->with( 'schedule', $schedule );
        
    }
    
    public function edit( Schedule $schedule ) {
        
        $this->authorize( 'update', $schedule );
        
        
        $input    = new DateTimeInput( $schedule );
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

            // $gcal_syncs = GCalSync::getBySchedule( $old_schedule );
            // GCalUnSyncWithGcalSyncsJob::dispatch( $gcal_syncs );  // 旧カレンダーの同期解除
            GsyncScheduleUnSyncJob::dispatch( $old_schedule, $old_schedule->calendar );  // 旧カレンダーの同期解除
            GsyncScheduleCreatedJob::dispatch( $schedule );       // 新カレンダーに同期
        }

        // イベント発行
        //
        // event( new ScheduleUpdatedEvent( $schedule ));
        
        // session()->regenerateToken();
        // BackButton::removePreviousSession();
        // session()->flash( 'flash_message', "スケジュール". $request['name']. "を修正しました。" );
        // return redirect()->route( 'groupware.schedule.show', [ 'schedule' =>  $schedule ]);
        return view( 'groupware.schedule2.show' )->with( 'schedule', $schedule );

    }
    
    public function show( Schedule $schedule ) {
        
        $access_list = $schedule->calendar->access_list();
        // dump( $access_list->isOwner( user_id() ));
        // dump( $access_list->isWriter( user_id() ));
        // dump( $access_list->isReader( user_id() ));
        // dump( $access_list->CanWrite( user_id() ));
        // dump( $access_list->canRead( user_id() ));

        // dump( $schedule->calendar );
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
        return redirect()->route( 'groupware.schedule.index' );
        // return $this->index( request() );
    }
    
}

//　フォーム用の入力値成形用関数
//
class DateTimeInput {
    
    public $start_date;
    public $start_time;
    public $start;
    
    public $end_date;  // string
    public $end_time;  // string
    public $end;  // dateTime 
    
    public $all_day;
    
    public function __construct( $input = null ) {

        $this->all_day = ( op( $input )->all_day ) ? 1 : 0;
        
        if( $input instanceof Schedule ) {
            // dump( __METHOD__, 'Schedule' );

            $this->start      = $this->start;
            $this->end        = $this->end;
            $this->start_date = $input->start_date;
            $this->end_date   = $input->end_date;

            if( $input->all_day ) {
                $this->start_time = null;
                $this->end_time   = null;
            } else {
                $this->start_time = $input->start->format( 'H:i' );
                $this->end_time   = $input->end->format( 'H:i' );
            }
            
        } else {
            // dump( __METHOD__, 'null');
            $now = Carbon::now();
            $this->start = new Carbon( $now->format( 'Y-m-d H:00'));
            $this->end   = new Carbon( $now->addHour()->format( 'Y-m-d H:00' ));
            
            $this->start_date = $this->start->format( 'Y-m-d' );
            $this->start_time = $this->start->format( 'H:i'   );
            $this->end_date   = $this->end->format( 'Y-m-d' );
            $this->end_time   = $this->end->format( 'H:i' );
        }
        // dump( $this , $input);
    }

}

//　カレンダー月表示用の日付データの生成
//
function getMonthlyCalendarDates( Carbon $base_date ) {
   
    $date = new Carbon( "{$base_date->year}-{$base_date->month}-01" );
    
    // MEMO: 月末が日曜日の場合の挙動を修正
    $addDay = ( $date->copy()->endOfMonth()->isSunday()) ? 7 : 0;
    
    // カレンダーを四角形にするため、前月となる左上の隙間用のデータを入れるためずらす
    $date->subDay( $date->dayOfWeek );

    // 同上。右下の隙間のための計算。
    // MEMO: 変数に修正
    // $count = 31 + $date->dayOfWeek;
    $count = 31 + $addDay + $date->dayOfWeek;
    $count = ceil($count / 7) * 7;
    $dates = [];

    for ($i = 0; $i < $count; $i++, $date->addDay()) {
        // copyしないと全部同じオブジェクトを入れてしまうことになる
        $dates[] = $date->copy();
    }
    return $dates;
        
}

//　キーが日付、値がscheuled_idの配列を作る（カレンダー表示で使うためのデータ）
//
function get_array_dates_schedule_id( $schedules ) {
    $dates = [];
    $i = 1;
    foreach( $schedules as $i => $schedule ) {

        // dump( "$i, $schedule->id" );        
        $start_date = new Carbon( $schedule->start_date );
        $end_date   = new Carbon( $schedule->end_date   );
        
        for( $date = $start_date->copy(); $date->lte( $end_date ); $date->addDay() ) {

            $d = $date->format( 'Y-m-d' );
            if( array_key_exists( $d, $dates )) {
                array_push( $dates[$d], $schedule->id );
            } else {
                $dates[$d] = [ $schedule->id ];
            }
            // if( $i >= 100 ) { break; }
            $i++;
        }
        // if( $i >= 100 ) { break; }

    }
    // dump( $dates );
    return $dates;        
}

