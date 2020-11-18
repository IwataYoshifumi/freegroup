<?php

namespace App\myHttp\GroupWare\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use DB;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Tasks;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Exception;
use Spatie\GoogleCalendar\Event;

use App\Http\Helpers\MyGoogleCalendar;
use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\File as MyFile;

class Schedule extends Model {

    // use SoftDeletes;
    
    const SEARCH_MODE = [ 0 => '作成者ベースで検索' , 1 =>  '関連社員で検索' , 2 => '作成者・関連社員　両方で検索' ];
    
    protected $fillable = [
        'name', 'place', 'start_time', 'end_time', 'period', 'memo', 'notice', 'schedule_type_id', 'user_id'
 
    ];

    // protected $hidden = [];

    protected $dates = [ 'start_time', 'end_time' ];
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  リレーションの定義
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    // 作成者
    public function user() {
        return $this->belongsTo( User::class, 'user_id' );
    }
    
    public function schedule_type() {
        return $this->belongsTo( ScheduleType::class, 'schedule_type_id' );
    }
    
    public function type() {
        return $this->schedule_type();
    }
    
    //　関連社員
    public function users() {
        // return $this->belongsToMany( User::class, 'relations', 'schedule_id', 'user_id' );
        // return $this->belongsToMany( User::class, 'r_schedules_users', 'schedule_id', 'user_id' );
        // return $this->morphedByMany( User::class, 'scheduleable' );
        return $this->morphedByMany( User::class, 'scheduleable' )->withPivot( 'google_calendar_event_id' );
    }
    
    public function customers() {
        // return $this->belongsToMany( Customer::class, 'relations', 'schedule_id', 'customer_id' );
        // return $this->belongsToMany( Customer::class, 'r_schedules_customers', 'schedule_id', 'customer_id' );
        return $this->morphedByMany( Customer::class, 'scheduleable' );
    }

    public function reports() {
        // return $this->belongsToMany( Report::class, 'relations', 'schedule_id', 'report_id' );
        // return $this->belongsToMany( Report::class, 'r_schedules_reports', 'schedule_id', 'report_id' );
        return $this->morphToMany( Report::class, 'reportable' );
    }
    
    public function files() {
        // return $this->belongsToMany( MyFile::class, 'r_files_schedules', 'schedule_id', 'file_id' );
        return $this->morphToMany( MyFile::class, 'fileable' );
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  確認メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function isset_google_calendar() {
        return $this->schedule_type->isset_google_calendar();
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  表示用関数
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function p_time() {
        if( $this->period == "時間" ) {
            $print = $this->user->name.": ".$this->start_time->format('H:i')." ".$this->name;
        } else {
            $print = $this->user->name.":終日 ".$this->name;
        }
        return $print;
    }
    
    public function print_time() {
        $start_time = $this->start_time;
        $end_time   = $this->end_time;
        
        if( $this->period == "時間" ) {
            if( $start_time->diffInMinutes( $end_time ) == 0 ) {
                $print = $start_time->format( 'Y-m-d H:i' );
            } else {
                $diff = $start_time->diffInHours( $end_time );
                if( $diff >= 1 ) {
                    $diff .= "時間";
                } else {
                    $diff = $start_time->diffInMinutes( $end_time )."分";
                }
                $print = $start_time->format( 'Y-m-d' )."  ".$start_time->format('H:i')."～".$end_time->format('H:i')."　（".$diff."）";
            }
            
        } elseif( $this->period == "終日" ) {
            if( ! $start_time->diffInMinutes( $end_time )) {
                $print = $start_time->format( 'Y-m-d H:i' )."  （終日）";
            } else {
                $print = $start_time->format( 'Y-m-d H:i' )."～".$end_time->format( 'H:i' )." （終日）";
            }
            
        } elseif( $this->period == "複数日") {
            $diff = $start_time->diffInDays( $end_time );
            if( $diff >= 1 ) { 
                $print = $start_time->format('Y-m-d H:i')."～".$end_time->format('Y-m-d H:i')."  （".$diff." 日間）";
            } else {
                $print = $start_time->format('Y-m-d H:i')."～".$end_time->format('Y-m-d H:i');
            }
        } else {
            abort( 500, "Schedule:print_time Error 1");
        }
        return $print;
    }
    
    public function print_start_time() {
        
        $start_time = $this->start_time;
        $end_time   = $this->end_time;

        if( $this->period == "時間" ) {
            $print = $start_time->format( 'Y-m-d H:i' );

        } elseif( $this->period == "終日" ) {
            $print = $start_time->format( 'Y-m-d' )."  （終日）";

        } elseif( $this->period == "複数日") {
            $print = $start_time->format('Y-m-d H:i')."～".$end_time->format('Y-m-d');
        } else {
            abort( 500, "Schedule:print_start_time Error 1");
        }
        return $print;

    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //    
    //　出力用関数（ブラウザの入力フォーム(datetime-local)に値が渡せる日付フォーマットを出力
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function o_start_time() {
        if( ! $this->start_time ) { return null; }
        
        $time = new Carbon( $this->start_time );
        return $time->format( 'Y-m-d\TH:i');
    }

    public function o_end_time() {
        if( ! $this->end_time ) { return null; }
        
        $time = new Carbon( $this->end_time );
        return $time->format( 'Y-m-d\TH:i');
    }

    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //　検索する
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    
    //　作成した人、関連者をまとめて検索
    //
    //  $search_mode = 0  スケジュール作成者のみを検索
    //  $search_mode = 1  スケジュール関連者のみを検索
    //  $search_mode = 2  スケジュール作成者・関連者を検索（関連者は重複削除）
    //  
    //  $schedules :: 作成者を検索
    //  $schedules :: 関連者を検索
    
    static public function search( $find, $search_mode = null, $sort = null, $asc_desc = null ) {
        // dump( $find );
        $start_date = Carbon::parse( $find['start_date'] )->format( 'Y-m-d 00:00:00' );
        $end_date   = Carbon::parse( $find['end_date']   )->format( 'Y-m-d 23:59:59' );
        
        $schedules = Schedule
                    //->selectRaw(  ' \'作成者\' as tag ,  id, start_time, end_time, user_id, name' )
                    // ->where( 'user_id', 2 )
                    ::where( function( $sub_query ) use ( $start_date, $end_date ) {
                                $sub_query->where( function( $query ) use ( $start_date, $end_date ) {
                                            $query->where( 'start_time', '>=', $start_date )
                                                  ->where( 'start_time', '<=', $end_date   );
                                            });
                                $sub_query->orWhere( function( $query ) use( $start_date, $end_date ) {
                                            $query->where( 'end_time', '>=', $start_date )
                                                  ->where( 'end_time', '<=', $end_date   );
                                            });
                                $sub_query->orWhere( function( $query ) use( $start_date, $end_date) {
                                            $query->where( 'start_time', '<', $start_date )
                                                  ->where( 'end_time',   '>', $end_date   );
                                            });
                    });

        $schedules2 = clone $schedules;

        // 件名検索
        if( ! empty( optional( $find )['name'] )) {
            $find_name = "%".$find['name']."%";
            $schedules  = $schedules ->where( 'name', 'like', $find_name );
            $schedules2 = $schedules2->where( 'name', 'like', $find_name );
        }

        //  日報　あり・なし
        //
        // dump( $find );
        if( ! empty( $find['has_reports'])) {
            // dump( $find );
            if( $find['has_reports'] == 1 ) {
                //　日報あり
                //
                $schedules = $schedules->has( 'reports' );
                $schedules2= $schedules2->has( 'reports');
            } elseif( $find['has_reports'] == -1 ) {
                //  日報なし
                //
                $schedules = $schedules->doesntHave( 'reports' );
                $schedules2= $schedules2->doesntHave( 'reports');
            }
        }

        //　社員検索
        //
        if( array_key_exists( 'users', $find ) and is_array( $find['users'] ) and ! empty( $find['users'][0] )) {
            $schedules  = $schedules ->whereIn( 'user_id', $find['users']);
            
            $schedules2 = $schedules2->whereHas( 'users', function( $query ) use ( $find ) {
                                // $query->whereIn( 'user_id', $find['users'] );
                                $query->whereIn( 'scheduleable_id', $find['users'] );
                            });
            // dump( $schedules, $schedules2 );
        } else {
            //　部署検索
            //
            if( ! empty( $find['dept_id'] )) {
                //  dump( 'dept_id', $find['dept_id'] );
                
                $sub_query = DB::table( 'users' )->select( 'id' )->where( 'dept_id', $find['dept_id'] );
    
                $schedules  = $schedules ->whereIn( 'user_id', $sub_query );
                $schedules2 = $schedules2->whereHas( 'users', function( $query ) use ( $sub_query ) {
                        // $query->whereIn( 'user_id', $sub_query );
                        $query->whereIn( 'scheduleable_id', $sub_query );

                    });
                
            } else {
                
                //　部署検索もなければ、ログインＩＤで検索
                //
                $schedules  = $schedules ->where( 'user_id', auth('user')->id() );
                
                $schedules2 = $schedules2->whereHas( 'users', function( $query ) {
                                    // $query->where( 'user_id', auth('user')->id() );
                                        $query->where( 'scheduleable_id', auth('user')->id() );

                                });
                // dump( 'search login ID', auth('user')->id());
                // dump( 'search login ID', auth('user')->id(), $schedules, $schedules2 );

            }
        }
        
        // dump( $schedules, $schedules2 );
        
        //　検索実行
        //
        if( empty( $search_mode )) {
            //
            //　作成者ベースで検索
            //
            $returns = $schedules->selectRaw(  ' \'作成者\' as tag ,  id, name, place, start_time, end_time, period, notice, memo, user_id, schedule_type_id' )
                                 ->with(['user', 'schedule_type' ])->orderBy( 'start_time' )->get();
            // dump( 'search_mode 0');
        } elseif( $search_mode == 1 ) {
            //
            //  関連者ベースで検索
            //
            $returns = $schedules2->selectRaw(  ' \'関連者\' as tag ,  id, name, place, start_time, end_time, period, notice, memo, user_id, schedule_type_id' )
                                  ->with([ 'users', 'user', 'schedule_type' ])->orderBy( 'start_time' )->get();
            // dump( 'search_mode 1');
            // dump( $schedules2 );
        } elseif( $search_mode == 2 ) {
            //
            //  作成者・関連者両方で検索（関連者は重複削除）
            //
            // dump( 'search_mode 2 ');
            $sub_query = clone $schedules;

            $schedules2= $schedules2->selectRaw(  ' \'関連者\' as tag ,  id, name, place, start_time, end_time, period, notice, memo, user_id, schedule_type_id' )
                                    ->whereNotIn( 'id', $sub_query->select( 'id' ) )
                                    ->with(['users', 'user', 'schedule_type' ]);

            $schedules = $schedules ->selectRaw(  ' \'作成者\' as tag ,  id, name, place, start_time, end_time, period, notice, memo, user_id, schedule_type_id' )
                                    ->with([ 'user', 'schedule_type' ]);
                                    
            $returns = $schedules->union( $schedules2 )->orderBy( 'start_time' )->get();        

            // $schedules = $schedules->with( 'user' )
            //                       ->union( $schedules2 )
            //                       ->with( 'users' )
            //                       ->orderBy( 'start_time' )->get();
        }

        // dump( $returns->all() );
        return $returns;
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //    
    //　スケジュールのコレクションから、キーが日付、値がID、の配列を作る（カレンダー表示で使うためのデータ）
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    static public function get_array_dates_schedule_id( $schedules ) {
        
        $dates = [];
        $i = 1;
        foreach( $schedules as $schedule ) {
            $start_date = Carbon::createFromFormat( 'Y-m-d H:i', $schedule->start_time->format( 'Y-m-d 00:00' ));
            $end_date   = Carbon::createFromFormat( 'Y-m-d H:i', $schedule->end_time->format(   'Y-m-d 23:59' ));
            
            for( $date = $start_date->copy(); $date->lte( $end_date ); $date->addDay() ) {
                
                $d = $date->format( 'Y-m-d' );
                if( array_key_exists( $d, $dates )) {
                    array_push( $dates[$d], $schedule->id );
                } else {
                    $dates[$d] = [ $schedule->id ];
                }
                // dump( 'ID:'.$schedule->id."  date:".$date->format( 'Y-m-d')."   start:".$start_date->format( 'Y-m-d')."   end_date:".$end_date->format( 'Y-m-d') );
                if( $i >= 100 ) { break; }
                $i++;
            }
            if( $i >= 100 ) { break; }

        }
        // dump( $dates );
        return $dates;        
        
    }
    
    static public function get_array_for_search_mode() {
        return self::SEARCH_MODE;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////
    //    
    //　Google カレンダー同期関連
    //
    /////////////////////////////////////////////////////////////////////////////////////////////

    public function create_google_calendar() {
        $g = new MyGoogleCalendar();
        $event_id = $g->create( $this );
        return $event_id;
    }

    public function update_google_calendar() {
        $g = new MyGoogleCalendar();
        if( ! empty( $this->google_calendar_event_id )) {
            $event_id = $g->update( $this, $this->google_calendar_event_id );
        } else {
            $event_id = $g->create( $this );
        }
        return $event_id;
    }

    public function delete_google_calendar() {
        $g = new MyGoogleCalendar();
        if( ! empty( $this->google_calendar_event_id )) {
            $event_id = $g->delete( $this, $this->google_calendar_event_id );
            return $event_id;
        } else {
            return null;
        }
    }
    
    // 変更前のUserクラスのコレクション
    public function sync_google_related_calendars( $old_users ) {
        
        $new_users = Schedule::find( $this->id )->users;
        $users_keys = array_unique( Arr::collapse( [$new_users->modelKeys(), $old_users->modelKeys()] ));

        
        // dump( $users_keys );
        // dump( $old_users, $new_users );

        $delete_users = $old_users->diff( $new_users );
        $create_users = $new_users->diff( $old_users );
        $update_users = $new_users->intersect( $old_users );
        
        // dump( $old_users->modelKeys(), $new_users->modelKeys(), $delete_users->modelKeys(), $create_users->modelKeys(), $update_users->modelKeys(), $users_keys );
        
        $types = ScheduleType::whereIn( 'user_id', $users_keys )->where( 'class', 'relation' )->whereNotNull( 'google_calendar_id' )->get();
        // $types = ScheduleType::whereIn( 'user_id', $users_keys )->get();
        // dump( $types );


    }

    public function delete_google_related_calendars() {
        
    }
        
    
}
