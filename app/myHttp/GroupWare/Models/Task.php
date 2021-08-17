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

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\File as MyFile;

class Task extends Model {

    // use SoftDeletes;
    
    const SEARCH_MODE = [ 0 => '作成者ベースで検索' , 1 =>  '関連社員で検索' , 2 => '作成者・関連社員　両方で検索' ];
    
    protected $fillable = [
        'user_id', 'tasklist_id',
        'name', 'place', 
        'start_time', 'end_time', 'period', 
        'memo', 
        'notice', 
        'permission',
 
    ];

    // protected $hidden = [];

    protected $dates = [ 'due_date', 'due_time', 'completed_time' ];
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  リレーションの定義
    //
    /////////////////////////////////////////////////////////////////////////////////////////////

    public function user() {
        return $this->belongsTo( User::class, 'user_id' );
    }
    public function creator() {
        return $this->user();
    }
    public function updator() {
        return $this->belongsTo( User::class, 'updator_id' );
    }
    
    public function tasklist() {
        return $this->belongsTo( TaskList::class, 'tasklist_id' );
    }
    
    public function complete_user() {
        return $this->belongsTo( User::class, 'user_who_complete' );        
    }


    //　関連社員
    //
    public function users() {
        // return $this->morphedByMany( User::class, 'taskable' )->withPivot( 'google_tasklist_event_id' );
        return $this->morphedByMany( User::class, 'taskable' );
    }
    public function attendees() {
        return $this->users();
    }
    
    public function customers() {
        return $this->morphedByMany( Customer::class, 'taskable' );
    }

    // public function reports() {
    //     return $this->morphToMany( Report::class, 'reportable' );
    // }
    
    public function files() {
        return $this->morphToMany( MyFile::class, 'fileable' );
    }
    
    
    
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  値取得メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function taskprop() {
        // $user    = ( empty( $user )) ? user_id() : $user ;
        // $user_id = ( $user instanceof User ) ? $user->id : $user;
        // // return $this->tasklist->taskprops()->where( 'user_id', $user_id )->first();
        return $this->tasklist->taskprops()->where( 'user_id', user_id() );
    }

    public function my_taskprop() {
        return $this->taskprop()->first();
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

    public function canRead( $user ) {
        $user = ( $user instanceof User ) ? $user : User::find( $user ); 

        if( $user->id == $this->user_id or
            $this->isAttendee( $user ) or 
            $this->tasklist->canRead( $user->id ) ) {
            return true;
        }
        return false;
    }
    
    public function canUpdate( $user ) {
        die( __METHOD__. ' Undefine ');
    }
    
    public function canDelete( $user ) {
        
    }

    public function isComplete() {
        return ( $this->status == "完了" ) ? 1 : 0;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  定数取得メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public static function getPermissions() {
        return config( 'groupware.task.permissions' );
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  表示用関数( 主に View ファイルで使用 )
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function p_due() {
        if( $this->all_day ) {
            return $this->due_date;
        } else {
            return $this->due_time->format( 'Y-m-d H:i' );
        }
    }
    
    public function p_time_for_daily_form() {
    
        if( $this->all_day ) { 
            $return = ""; 
        } else {
           $return = $this->due_time->format( 'G:i' );
        }
        if( $this->status == "完了" ) {
            $return .= "【済】";
        }
        return $return;
    }
    
    public function p_time_for_montly_form() {
        if( $this->all_day ) { return "aa"; }
        return $this->due_time->format( 'G:i' );
    }
    
    public function p_time( $form_type = null ) {
        
        if( $form_type == 'daily' ) {
            return $this->p_time_for_daily_form();
            
        } elseif( $form_type == 'weekly' ) {
            return $this->p_time_for_daily_form();

        } elseif( $form_type == 'monthly' ) {
            return $this->p_time_for_montly_form();

        } elseif( $form_type == 'index' ) {

        } elseif( $form_type == 'detail' ) {
            
        } 
        
        return $this->p_due();
    }
    
    public function p_time_in_daily_form() {
        if( $this->all_day ) {
            return '';
        } else {
            return $this->due_time->format( 'H:i' );
        }
    }
    
    public function style() {
        return $this->my_taskprop()->style();
    }

    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //　検索する
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    
    //　作成した人、関連者をまとめて検索
    //
    //  $search_mode = 0  タスク作成者のみを検索
    //  $search_mode = 1  タスク関連者のみを検索
    //  $search_mode = 2  タスク作成者・関連者を検索（関連者は重複削除）
    //  
    //  $tasks :: 作成者を検索
    //  $tasks :: 関連者を検索
    
    static public function search( $find, $search_mode = null, $sort = null, $asc_desc = null ) {
        // if_debug( $find );
        $start_date = Carbon::parse( $find['start_date'] )->format( 'Y-m-d 00:00:00' );
        $end_date   = Carbon::parse( $find['end_date']   )->format( 'Y-m-d 23:59:59' );
        
        $tasks = Task
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

        $tasks2 = clone $tasks;

        // 件名検索
        if( ! empty( optional( $find )['name'] )) {
            $find_name = "%".$find['name']."%";
            $tasks  = $tasks ->where( 'name', 'like', $find_name );
            $tasks2 = $tasks2->where( 'name', 'like', $find_name );
        }

        //  日報　あり・なし
        //
        // if_debug( $find );
        if( ! empty( $find['has_reports'])) {
            // if_debug( $find );
            if( $find['has_reports'] == 1 ) {
                //　日報あり
                //
                $tasks = $tasks->has( 'reports' );
                $tasks2= $tasks2->has( 'reports');
            } elseif( $find['has_reports'] == -1 ) {
                //  日報なし
                //
                $tasks = $tasks->doesntHave( 'reports' );
                $tasks2= $tasks2->doesntHave( 'reports');
            }
        }

        //　社員検索
        //
        if( array_key_exists( 'users', $find ) and is_array( $find['users'] ) and ! empty( $find['users'][0] )) {
            $tasks  = $tasks ->whereIn( 'user_id', $find['users']);
            
            $tasks2 = $tasks2->whereHas( 'users', function( $query ) use ( $find ) {
                                // $query->whereIn( 'user_id', $find['users'] );
                                $query->whereIn( 'taskable_id', $find['users'] );
                            });
            // if_debug( $tasks, $tasks2 );
        } else {
            //　部署検索
            //
            if( ! empty( $find['dept_id'] )) {
                //  if_debug( 'dept_id', $find['dept_id'] );
                
                $sub_query = DB::table( 'users' )->select( 'id' )->where( 'dept_id', $find['dept_id'] );
    
                $tasks  = $tasks ->whereIn( 'user_id', $sub_query );
                $tasks2 = $tasks2->whereHas( 'users', function( $query ) use ( $sub_query ) {
                        // $query->whereIn( 'user_id', $sub_query );
                        $query->whereIn( 'taskable_id', $sub_query );

                    });
                
            } else {
                
                //　部署検索もなければ、ログインＩＤで検索
                //
                $tasks  = $tasks ->where( 'user_id', auth('user')->id() );
                
                $tasks2 = $tasks2->whereHas( 'users', function( $query ) {
                                    // $query->where( 'user_id', auth('user')->id() );
                                        $query->where( 'taskable_id', auth('user')->id() );

                                });
                // if_debug( 'search login ID', auth('user')->id());
                // if_debug( 'search login ID', auth('user')->id(), $tasks, $tasks2 );

            }
        }
        
        // if_debug( $tasks, $tasks2 );
        
        //　検索実行
        //
        if( empty( $search_mode )) {
            //
            //　作成者ベースで検索
            //
            $returns = $tasks->selectRaw(  ' \'作成者\' as tag ,  id, name, place, start_time, end_time, period, notice, memo, user_id, task_type_id' )
                                 ->with(['user', 'task_type' ])->orderBy( 'start_time' )->get();
            // if_debug( 'search_mode 0');
        } elseif( $search_mode == 1 ) {
            //
            //  関連者ベースで検索
            //
            $returns = $tasks2->selectRaw(  ' \'関連者\' as tag ,  id, name, place, start_time, end_time, period, notice, memo, user_id, task_type_id' )
                                  ->with([ 'users', 'user', 'task_type' ])->orderBy( 'start_time' )->get();
            // if_debug( 'search_mode 1');
            // if_debug( $tasks2 );
        } elseif( $search_mode == 2 ) {
            //
            //  作成者・関連者両方で検索（関連者は重複削除）
            //
            // if_debug( 'search_mode 2 ');
            $sub_query = clone $tasks;

            $tasks2= $tasks2->selectRaw(  ' \'関連者\' as tag ,  id, name, place, start_time, end_time, period, notice, memo, user_id, task_type_id' )
                                    ->whereNotIn( 'id', $sub_query->select( 'id' ) )
                                    ->with(['users', 'user', 'task_type' ]);

            $tasks = $tasks ->selectRaw(  ' \'作成者\' as tag ,  id, name, place, start_time, end_time, period, notice, memo, user_id, task_type_id' )
                                    ->with([ 'user', 'task_type' ]);
                                    
            $returns = $tasks->union( $tasks2 )->orderBy( 'start_time' )->get();        

            // $tasks = $tasks->with( 'user' )
            //                       ->union( $tasks2 )
            //                       ->with( 'users' )
            //                       ->orderBy( 'start_time' )->get();
        }

        // if_debug( $returns->all() );
        return $returns;
    }
    
    static public function get_array_for_search_mode() {
        return self::SEARCH_MODE;
    }


        
    
}
