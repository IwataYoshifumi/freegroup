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
        'user_id', 'calendar_id',
        'name', 'place', 
        'start_time', 'end_time', 'period', 
        'memo', 
        'notice', 
        'permission',
 
    ];

    // protected $hidden = [];

    protected $dates = [ 'start', 'end' ];
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  リレーションの定義
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    // 作成者
    public function user() {
        return $this->belongsTo( User::class, 'user_id' );
    }
    public function creator() {
        return $this->user();
    }
    public function updator() {
        return $this->belongsTo( User::class, 'updator_id' );
    }
    
    public function calendar() {
        return $this->belongsTo( Calendar::class, 'calendar_id' );
    }

    public function gcal_syncs() {
        return $this->hasMany( GCalSync::class, 'schedule_id' );
    }
    
    //　関連社員
    public function users() {
        // return $this->morphedByMany( User::class, 'scheduleable' )->withPivot( 'google_calendar_event_id' );
        return $this->morphedByMany( User::class, 'scheduleable' );
    }
    public function attendees() {
        return $this->users();
    }
    
    public function customers() {
        return $this->morphedByMany( Customer::class, 'scheduleable' );
    }

    public function reports() {
        return $this->morphToMany( Report::class, 'reportable' );
    }
    
    public function files() {
        return $this->morphToMany( MyFile::class, 'fileable' );
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  値取得メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function calprop( $user = null ) {
        $user    = ( empty( $user )) ? user_id() : $user ;
        $user_id = ( $user instanceof User ) ? $user->id : $user;
        return $this->calendar->calprops()->where( 'user_id', $user_id )->first();
    }

    public function my_calprop() {
        return $this->calprop( user_id() );
    }

    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  確認メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function isAttendee( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user; 
        return $this->users()->where( 'id', $user_id )->count() === 1;
    }

    public function isCreator( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user; 
        return $this->creator->id === $user_id;
    }

    public function isUpdator( $user ) {
        $user_id = ( $user instanceof User ) ? $user->id : $user; 
        return $this->updator->id === $user_id;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  定数取得メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public static function getPermissions() {
        return config( 'groupware.schedule.permissions' );
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  表示用関数( 主に View ファイルで使用 )
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function start_time() {
        if( $this->all_day ) { return "【終日】"; }
        return $this->start->format( 'H:i' );
    }

    public function end_time() {
        if( $this->all_day ) { return null; }
        return "～ ". $this->end_time( 'H:i' );
    }

    public function p_dateTime() {
        if( $this->all_day ) { 
            if( $this->start->eq( $this->end )) { 
                return $this->start->format( 'Y-m-d' );
            } else {
                return $this->start->format( 'Y-m-d' ) . ' ～ ' . $this->end->format( 'Y-m-d' );
            }
        } else {
            if( $this->start->eq( $this->end )) { 
                return $this->start->format( 'Y-m-d H:i' );
            } else {
                return $this->start->format( 'Y-m-d H:i' ) . ' ～ ' . $this->end->format( 'Y-m-d H:i' );
            }
        }
    }

    public function p_time() {
        return $this->p_dateTime();
    }

    public function print_time() {
        return $this->start_time();        
    }

    public function print_start_time() {
        return $this->start_time();
    }
    
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
    
    // /////////////////////////////////////////////////////////////////////////////////////////////
    // //    
    // //　スケジュールのコレクションから、キーが日付、値がID、の配列を作る（カレンダー表示で使うためのデータ）
    // //
    // /////////////////////////////////////////////////////////////////////////////////////////////
    // static public function get_array_dates_schedule_id( $schedules ) {
        
    //     $dates = [];
    //     $i = 1;
    //     foreach( $schedules as $schedule ) {
    //         $start_date = Carbon::createFromFormat( 'Y-m-d H:i', $schedule->start_time->format( 'Y-m-d 00:00' ));
    //         $end_date   = Carbon::createFromFormat( 'Y-m-d H:i', $schedule->end_time->format(   'Y-m-d 23:59' ));
            
    //         for( $date = $start_date->copy(); $date->lte( $end_date ); $date->addDay() ) {
                
    //             $d = $date->format( 'Y-m-d' );
    //             if( array_key_exists( $d, $dates )) {
    //                 array_push( $dates[$d], $schedule->id );
    //             } else {
    //                 $dates[$d] = [ $schedule->id ];
    //             }
    //             // dump( 'ID:'.$schedule->id."  date:".$date->format( 'Y-m-d')."   start:".$start_date->format( 'Y-m-d')."   end_date:".$end_date->format( 'Y-m-d') );
    //             if( $i >= 100 ) { break; }
    //             $i++;
    //         }
    //         if( $i >= 100 ) { break; }

    //     }
    //     // dump( $dates );
    //     return $dates;        
        
    // }
    
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
