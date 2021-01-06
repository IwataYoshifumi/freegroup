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

use App\Http\Helpers\BackButton;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use App\Http\Helpers\MyGoogleCalendar;


use App\myHttp\GroupWare\Requests\ScheduleRequest;
use App\myHttp\GroupWare\Events\SyncRelatedScheduleToGoogleCalendarEvent;

use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\File as MyFile;


class ScheduleController extends Controller {
    
    // 　ルーティングコントローラー
    //
    public function index( Request $request ) {
        return self::index_monthly( $request );
    }
    
    public function index_monthly( Request $request ) {
        
        // dump( $request->all() );
        $find = [];
        $show = [];
        $base_date = ( empty( $request->base_date )) ? Carbon::today() : new Carbon( $request->base_date );
        
        // $search_mode = ( isset( $request->search_mode )) ? $request->search_mode : 2;
        
        // dump( $base_date );
        
        //　表示する月カレンダーデータ取得
        //
        $dates = ScheduleController::getMonthlyCalendarDates( $base_date );
    
        //　期間中の予定を検索
        //
        $find['start_date'] = Arr::first( $dates )->format( 'Y-m-d' );
        $find['end_date']   = Arr::last( $dates  )->format( 'Y-m-d' );
        $find['dept_id']    = optional( $request )->dept_id;
        $find['users']      = optional( $request )->users;
        $find['has_reports']= optional( $request )->has_reports;
        // dump( $find );

        $schedules = Schedule::search( $find, $request->search_mode );
        
        //　カレンダー表示用データの取得
        //
        $schedule_ids = Schedule::get_array_dates_schedule_id( $schedules );
        // dump( $schedule_ids, $schedules );
        
        BackButton::setHere( $request );
        // dump( $request->all(), session()->all() );
        return view( 'groupware.schedule.index_monthly')->with( 'request' , $request )
                                                        ->with( 'find', $find )
                                                        ->with( 'show', $show )
                                                        ->with( 'base_date', $base_date )
                                                        ->with( 'schedules', $schedules )
                                                        ->with( 'schedule_ids', $schedule_ids );
    }

    public function index_weekly( Request $request ) {
        // dump( $request->all() );
        $base_date = ( empty( $request->base_date )) ? Carbon::today() : new Carbon( $request->base_date );
        
        //　表示する週カレンダーデータ取得
        //
        $dates = ScheduleController::getWeeklyCalendarDates( $base_date );
        $find['start_date'] = Arr::first( $dates )->format( 'Y-m-d' );
        $find['end_date']   = Arr::last( $dates  )->format( 'Y-m-d' );
        $find['dept_id']    = $request->dept_id;
        $find['users']      = $request->users;
        $find['has_reports']= $request->has_reports;


        $schedules = Schedule::search( $find, $request->search_mode );
        #dump( $schedules->toArray() );
        
        //　カレンダー表示用データの取得
        //
        $schedule_ids = Schedule::get_array_dates_schedule_id( $schedules );
        
        BackButton::setHere( $request );
        return view( 'groupware.schedule.index_weekly' )->with( 'request' , $request )
                                                        ->with( 'dates',    $dates )
                                                        ->with( 'base_date', $base_date )
                                                        ->with( 'schedules', $schedules )
                                                        ->with( 'schedule_ids', $schedule_ids );
    }

    public function index_daily( Request $request ) {
        if( empty( $request->base_date )) { abort( 500, 'ScheduleController:index_daily' ); }
        // dump( $request->all() );
        
        $base_date = ( empty( $request->base_date )) ? Carbon::today() : new Carbon( $request->base_date );

        $find['start_date'] = $request->base_date;
        $find['end_date']   = $request->base_date;
        $find['dept_id']    = $request->dept_id;
        $find['users']      = $request->users;
        $find['has_reports']= $request->has_reports;

        $schedules = Schedule::search( $find, $request->search_mode );
        // dd( $schedules );

        BackButton::setHere( $request );
        return view( 'groupware.schedule.index_daily' )->with( 'request' ,  $request )
                                                       ->with( 'base_date', $base_date )
                                                       ->with( 'schedules', $schedules );
    }
    
    public function json_search( Request $request ) {

        $find['start_date'] = $request->start_date;
        $find['end_date']   = $request->end_date;
        $find['dept_id']    = $request->dept_id;
        $find['users']      = [ $request->users ];
        $find['name']       = $request->name;
        $search_mode        = ( $request->search_mode ) ? $request->search_mode : 0; 

        $schedules = Schedule::search( $find, $search_mode );
        //  dd( $request->all(), $search_mode, $schedules->all() );

        $array = [];
        foreach( $schedules as $s ) {
            array_push( $array, [   'id'         => $s->id,
                                    'name'       => $s->name,
                                    'p_time'     => $s->print_start_time(),
                                    'start_time' => $s->start_time,
                                    'end_time'   => $s->end_time,
                                    'place'      => $s->place,
                                    'user_name'  => $s->user->name,
                                    'user_dept'  => $s->user->dept->name,
                                    'url'        => route( 'groupware.schedule.show', ['schedule' => $s->id ] ),
                                    ] );
        }
        // dump( $request, $array );
        return response()->json( $array );
        
    }
    
    public function create( Request $request ) {
        $this->authorize( 'create', Schedule::class );

        // dump( session()->all() );
        // dump( $request->all() );
        
        //　初期値設定
        //
        $schedule = new Schedule;
        if( ! old( 'user_id' )) { 
            $defaults['period'] = '時間'; 
            $schedule->period = "時間";  
            $schedule->user_id = auth('user')->user()->id;
            $schedule->start_time = Carbon::today()->addHours(9);
            $schedule->end_time   = Carbon::today()->addHours(10);
            // dump( $schedule->o_start_time() ); 
        } else {
            $schedule->user_id = old( 'user_id' );
        }
        if( $request->start_time ) { 
            // dd( $request->start_time );
            $schedule->start_time = $request->start_time."T09:00"; 
            $schedule->end_time   = $request->start_time."T10:00"; 
        }
        // dump( $schedule, $defaults );
        // return view( 'groupware.schedule.create' )->with( 'defaults', $defaults );
        BackButton::stackHere( $request );
        return view( 'groupware.schedule.input' )->with( 'schedule', $schedule );
        
    }
    
    public function store( ScheduleRequest $request ) {

        $this->authorize( 'create', Schedule::class );
        
        $schedule = DB::transaction( function() use( $request ) {
            $schedule = new Schedule;

            $schedule->user_id  = auth('user')->id();
            $schedule->name     = $request->name;
            $schedule->place    = $request->place;
            $schedule->start_time = $request->start_time;
            $schedule->end_time  = $request->end_time;
            $schedule->period   = $request->period;
            $schedule->memo     = $request->memo;
            $schedule->schedule_type_id = $request->schedule_type_id;
            $schedule->save();
            
            // Google カレンダー同期
            if( $schedule->isset_google_calendar() ) {
                $google_event_id = $schedule->create_google_calendar();
                $schedule->google_calendar_event_id = $google_event_id;
                $schedule->save();
            }
    
            // 関連顧客・社員情報の同期        
            $schedule->customers()->sync( $request->customers );
            $schedule->users()->sync( $request->users );
           
            //　ファイル保存
            //
            $files = [];
            // foreach( $request->file('upload_files') as $i => $file ) {
            foreach( ( $request->file('upload_files')) ? $request->file('upload_files') : [] as $i => $file ) {
                // dump( "aaa", $i, $file );
                $path = $file->store('');
                $value = [ 'file_name' => $file->getClientOriginalName(), 'path' => $path, 'user_id' => auth('user')->user()->id ];
                $f = MyFile::create( $value );
                $files[$i] = $f->id;
            }
            // dd( $files );
            $schedule->files()->sync( $files );

            return $schedule;
        });
        
        // 関連社員のGoogleカレンダーの同期
        //
        // if( $schedule->has('users')->count() >= 1 ) {
        //     event( new SyncRelatedScheduleToGoogleCalendarEvent( $schedule ));
        // }

        // dump( 'request->all', $request->all(),  $schedule );
        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "スケジュール". $request['name']. "を追加しました。" );
        session()->regenerateToken();
        
        // return view( 'groupware.schedule.show' )->with( 'schedule', $schedule );
        return redirect()->route( 'groupware.schedule.show', [ 'schedule' =>  $schedule ]);
        
    }
    
    public function edit( Schedule $schedule ) {
        // dump( session()->all() );
        $this->authorize( 'update', [ $schedule, auth('user')->user() ]);
        
        
        BackButton::stackHere( request() );
        return view( 'groupware.schedule.input' )->with( 'schedule', $schedule );
    }
    
    public function update( Schedule $schedule, ScheduleRequest $request ) {
        $schedule = DB::transaction( function() use( $request, $schedule ) {

            $old_users = $schedule->users;

            // $schedule->user_id  = $request->user_id
            $schedule->name     = $request->name;
            $schedule->place    = $request->place;
            $schedule->start_time = $request->start_time;
            $schedule->end_time  = $request->end_time;
            $schedule->period   = $request->period;
            $schedule->memo     = $request->memo;
            $schedule->schedule_type_id = $request->schedule_type_id;
            $schedule->save();
            
            //　Googleカレンダーと同期
            if( $schedule->isset_google_calendar() ) {
                $event_id = $schedule->update_google_calendar();
                $schedule->google_calendar_event_id = $event_id;
                $schedule->save();
            }

            $schedule->customers()->sync( $request->customers );
            $schedule->users()->sync( $request->users );

            // 関連社員のGoogleカレンダー同期
            // $old_users = $schedule->users;
            // $new_users = Schedule::find( $schedule->id )->users;
            
            // dump( $old_users->modelKeys(), $new_users->modelKeys(), $new_users->intersect( $old_users)->modelKeys() );
            // dump( $new_users->diff( $old_users )->modelKeys(), $old_users->diff( $new_users)->modelKeys() );
            
            $schedule->sync_google_related_calendars( $old_users );

            //　アップロードファイル
            //
            $files = ( ! empty( $request->attached_files )) ? $request->attached_files : [] ;
            // dd( $request->file( 'upload_files' ));
            foreach( ( $request->file('upload_files')) ? $request->file('upload_files') : [] as $i => $file ) {
                // dump( "aaa", $i, $file );
                $path = $file->store('');
                $value = [ 'file_name' => $file->getClientOriginalName(), 'path' => $path, 'user_id' => auth('user')->user()->id ];
                $f = MyFile::create( $value );
                // $files[$i] = $f->id;
                array_push( $files, $f->id );
            }
            // dd( $files );
            $schedule->files()->sync( $files );
        
            return $schedule;
        });
        

        
        // 関連社員のGoogleカレンダーの同期
        //
        if( $schedule->has('users')->count() >= 1 ) {
            event( new SyncRelatedScheduleToGoogleCalendarEvent( $schedule ));
        }
        
        session()->regenerateToken();
        BackButton::removePreviousSession();
        session()->flash( 'flash_message', "スケジュール". $request['name']. "を修正しました。" );
        return view( 'groupware.schedule.show' )->with( 'schedule', $schedule );
        // return redirect()->route( 'groupware.schedule.show', [ 'schedule' =>  $schedule ]);
        
    }
    
    public function show( Schedule $schedule ) {
        BackButton::stackHere( request() );
        return view( 'groupware.schedule.show' )->with( 'schedule', $schedule );
    }
    public function show_m( Request $request ) {
        if( is_null( $request->id )) { abort( 403, 'ScheduleController:show_m' ); }
        return redirect()->route( 'groupware.schedule.show', [ 'schedule' => $request->id, 'request' => $request ] );        
    }
    public function detail() {
        
    }
    
    public function delete( Schedule $schedule ) {
        // session()->flash( 'info_message', "スケジュールを削除します。よろしいですか。" );
        BackButton::stackHere( request() );
        return view( 'groupware.schedule.delete' )->with( 'schedule' , $schedule );
    }
    public function deleted( Schedule $schedule ) {
        
        DB::transaction( function() use( $schedule ) {
            $schedule->customers()->detach();
            $schedule->delete_google_calendar();
            $schedule->delete();
        });
        
        session()->regenerateToken();
        BackButton::removePreviousSession();
        // session()->flash( 'info_message', "スケジュールを削除しました" );
        return view( 'groupware.schedule.delete' )->with( 'schedule' , $schedule );
    }
    
    //　カレンダー表示用の日付データの生成
    //
    static public function getMonthlyCalendarDates( Carbon $base_date ) {
   
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
    
    static public function getWeeklyCalendarDates( Carbon $base_date ) {
        
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

    //　View内で使う関数
    //
    static public function get_argv_for_forms( Request $request, $base_date = null ) {
        
        $argvs = [ 
           'dept_id'   => $request->dept_id,
           'users'     => $request->users,
           'search_mode' => $request->search_mode,
           ];


        if( is_null( $base_date )) {
            $argvs['base_date'] = Carbon::now()->format( 'Y-m-d' );
        } else {
            $argvs['base_date'] = $base_date;
        }
        // dump( $argvs );
        return $argvs;
    }
}
