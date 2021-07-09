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

class Schedule2IndexController extends Controller {
    
    // 　ルーティングコントローラー
    //
    public function index( Request $request ) {

        if( empty( $request->search_query )) { 
            $request->users = [ user_id() ]; 
            $request->calendar_auth = 'reader';
            $request->display_axis = 'calendar';

        } else {
            $return = SearchSchedule::search( $request );

            if_debug( $request->all(), $return );
            $users     = op( $return )['users'    ];
            $user_ids  = op( $return )['user_ids' ];
            $schedules = op( $return )['schedules'];
            $calendars = op( $return )['calendars'];
            $calprops  = op( $return )['calprops' ];
        }
        if( empty( $schedules )) { $schedules = []; }

        BackButton::setHere( $request );
        return view( 'groupware.schedule2.index' )->with( 'schedules', $schedules )
                                                //   ->with( 'find'   , $find    )
                                                  ->with( 'request', $request );
    }
    
    public function monthly( Request $request ) {
    
        if( empty( $request->search_query )) { 
            $request->users = [ user_id() ]; 
            $request->calendar_auth = 'reader';
            $request->search_condition = 'users';
            $request->display_axis = 'users';

        }
        //　表示する月カレンダーデータ取得
        //
        $base_date = ( empty( $request->base_date )) ? now() : new Carbon( $request->base_date );
        $dates = self::getMonthlyCalendarDates( $base_date );
        $request->start_date = Arr::first( $dates )->format( 'Y-m-d' );
        $request->end_date   = Arr::last( $dates  )->format( 'Y-m-d' );

        //  予定を検索
        //
        $returns = SearchSchedule::search( $request );

        //　表示用のデータ生成
        //
        $schedules    = ( isset( $returns['schedules'] )) ? $returns['schedules'] : [];
        $schedule_ids = self::get_array_dates_schedule_id( $schedules );
        if( count( $schedules ) == 0 ) { session()->flash( 'flash_message', 'この条件では何もスケジュールは検索されませんでした'); }

        // View をレンダー
        //
        // if_debug( $request, $returns );
        BackButton::setHere( $request );
        return view( 'groupware.schedule2.monthly')->with( 'request' , $request )
                                                   ->with( 'returns',  $returns )
                                                   ->with( 'base_date', $base_date )
                                                   ->with( 'dates', $dates )
                                                   ->with( 'schedule_ids', $schedule_ids )
                                                   ->with( 'schedules', $schedules );
    }

    public function weekly( Request $request ) {
        if( empty( $request->search_query )) { 
            $request->users = [ user_id() ]; 
            $request->calendar_auth = 'reader';
            $request->search_condition = 'users';
        }
        if( empty( $request->vertical_axis )) { $request->vertical_axis = 'calendars'; }

        //　表示する週カレンダーデータ取得
        //
        $base_date = ( empty( $request->base_date )) ? now() : new Carbon( $request->base_date );
        $dates = self::getWeeklyCalendarDates( $base_date );
        $request->start_date = Arr::first( $dates )->format( 'Y-m-d' );
        $request->end_date   = Arr::last( $dates  )->format( 'Y-m-d' );

        //  予定を検索
        //
        $returns = SearchSchedule::search( $request );
        
        //　表示用のデータ生成
        //
        $schedules    = ( isset( $returns['schedules'] )) ? $returns['schedules'] : [];
        $schedule_ids = self::get_array_dates_schedule_id( $schedules );
        if( count( $schedules ) == 0 ) { session()->flash( 'flash_message', 'この条件では何もスケジュールは検索されませんでした'); }

        // View をレンダー
        //
        // if_debug( $request, $returns );
        BackButton::setHere( $request );
        return view( 'groupware.schedule2.weekly_user' )->with( 'request' , $request )
                                                        ->with( 'returns',  $returns )
                                                        ->with( 'base_date', $base_date )
                                                        ->with( 'dates', $dates )
                                                        ->with( 'schedule_ids', $schedule_ids )
                                                        ->with( 'schedules', $schedules );
        
    }

    public function daily( Request $request ) {
        
        #if_debug( __METHOD__, $request->all() );
        
        if( empty( $request->search_query )) { 
            $request->users = [ user_id() ]; 
            $request->calendar_auth = 'reader';
            $request->search_condition = 'users';
            $request->display_axis = 'users';
        }
        $request->start_date = $request->base_date;
        $request->end_date   = $request->base_date;
        
        if( empty( $request->vertical_axis )) { $request->vertical_axis = 'users'; }
        $base_date = ( empty( $request->base_date )) ? now() : new Carbon( $request->base_date );

        //  予定を検索
        //
        $returns = SearchSchedule::search( $request );
        #if_debug( __METHOD__, $request->all(), $returns );
        
        if( count( $returns['schedules'] ) == 0 ) { session()->flash( 'flash_message', 'この条件では何もスケジュールは検索されませんでした'); }
        
        // View をレンダー
        //
        BackButton::stackHere( $request );
        return view( 'groupware.schedule2.daily_user' )->with( 'request' , $request )
                                                        ->with( 'returns',  $returns )
                                                        ->with( 'base_date', $base_date );
    }
    
    public function showModal( Schedule $schedule ) {
        $this->authorize( 'view', $schedule );
        
        return view( 'groupware.schedule2.show_modal' )->with( 'schedule', $schedule );
    }

    //　menu_button View内で使う関数
    //
    public static function get_argv_for_forms( Request $request, $base_date = null ) {
            
        foreach( $request->all() as $key => $value ) {
            if( $key == '_token' or $key == '_method' ) { continue; } 
            $argvs[$key] = $value;
        }
        if( is_null( $base_date )) {
            $argvs['base_date'] = Carbon::now()->format( 'Y-m-d' );
        } else {
            $argvs['base_date'] = $base_date;
        }
        return $argvs;
    }

    //　カレンダー表示用の日付データの生成
    //
    static private function getMonthlyCalendarDates( Carbon $base_date ) {
   
        $date = new Carbon( "{$base_date->year}-{$base_date->month}-01" );
        
        $first_of_month = $date->copy()->firstOfMonth();
        $end_of_month   = $date->copy()->endOfMonth();

        //　月表示カレンダーの表示表示範囲を取得（日曜が週の初め）
        //
        $first_date = $first_of_month->copy();
        $end_date   = $end_of_month->copy();
        while( ! $first_date->isSunday() ) { $first_date->subDay(); }
        while( ! $end_date->isSaturday() ) { $end_date->addDay();  }

        //　月表示カレンダーのデータ作成
        //
        $count = $first_date->diffInDays( $end_date );
        $dates = [];
        for ($i = 0; $i <= $count; $i++, $first_date->addDay()) {
            // copyしないと全部同じオブジェクトを入れてしまうことになる
            $dates[$i] = $first_date->copy();
        }
        if_debug( $dates );
        return $dates;
    }
    
    static private function getWeeklyCalendarDates( Carbon $base_date ) {
        
        $date = new Carbon( $base_date );
        // 曜日のチェック
        if( $date->isSunday() ) {
            $sunday = $date->copy();
        } elseif( $date->isMonday() ) {
            $sunday = $date->copy()->subDay( 1 );
        } elseif( $date->isTuesday() ) {
            $sunday = $date->copy()->subDay( 2 );
        } elseif( $date->isWednesday() ) {
            $sunday = $date->copy()->subDay( 3 );
        } elseif( $date->isThursday() ) {
            $sunday = $date->copy()->subDay( 4 );
        } elseif( $date->isFriday() ) {
            $sunday = $date->copy()->subDay( 5 );
        } elseif( $date->isSaturday() ) {
            $sunday = $date->copy()->subDay( 6 );
        }
        // dd( $date->format( 'Y-m-d D' ), $sunday->format( 'Y-m-d D' ) );

        // カレンダーを四角形にするため、前月となる左上の隙間用のデータを入れるためずらす
        $date->subDay( $date->dayOfWeek );

        for ($i = 0; $i <= 6 ; $i++, $sunday->addDay()) {
            // copyしないと全部同じオブジェクトを入れてしまうことになる
            // $dates[] = $sunday->copy()->format( 'Y-m-d D');
            $dates[] = $sunday->copy();
        }
        // dd( $dates );
        return $dates;
    }
    
    //　キーが日付、値がscheuled_idの配列を作る（カレンダー表示で使うためのデータ）
    //
    static private function get_array_dates_schedule_id( $schedules ) {
        $dates = [];
        $i = 1;
        foreach( $schedules as $i => $schedule ) {
    
            // if_debug( "$i, $schedule->id" );        
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
        // if_debug( $dates );
        return $dates;
    }
}