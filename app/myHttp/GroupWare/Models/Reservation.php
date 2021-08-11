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
use App\myHttp\GroupWare\Models\Facility;
use App\myHttp\GroupWare\Models\Schecule;

class Reservation extends Model {

    // use SoftDeletes;
    
    const SEARCH_MODE = [ 0 => '作成者ベースで検索' , 1 =>  '関連社員で検索' , 2 => '作成者・関連社員　両方で検索' ];
    
    protected $fillable = [
        'user_id', 'facility_id',
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
    
    public function facility() {
        return $this->belongsTo( Facility::class, 'facility_id' );
    }
    
    //
    public function schedules() {
        return $this->morphToMany( Schedule::class, 'scheduleable' );        
    }

    // public function users() {
    //     return $this->morphedByMany( User::class, 'reservationable' );
    // }

    // public function attendees() {
    //     return $this->users();
    // }
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  値取得メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////

    //　１つの予定は複数の予約を持つが、予約は１つの予定しか持たない
    //
    public function schedule() {
        return $this->schedules->first();
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
            // $this->isAttendee( $user ) or 
            $this->facility->canRead( $user->id ) ) {
            return true;
        }
        return false;
    }
    
    public function canUpdate( $user ) {
        die( __METHOD__. ' Undefine ');
    }
    
    public function canDelete( $user ) {
        
    }


    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  定数取得メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public static function getPermissions() {
        return config( 'groupware.reservation.permissions' );
    }
    
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  値取得メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////

    //　スケジュールの予定日数を取得（必ず１以上）
    //
    public function getNumDates() {
        $start = new Carbon( $this->start->format( 'Y-m-d' ));
        $end   = new Carbon( $this->end->format( 'Y-m-d' ));
        
        
        return $end->diffInDays( $start ) + 1;
    }
    
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  表示用関数( 主に View ファイルで使用 )
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function start_time() {
        if( $this->all_day ) { return "終日"; }
        return $this->start->format( 'H:i' );
    }

    public function end_time() {
        if( $this->all_day ) { return null; }
        return $this->end->format( 'H:i' );
    }

    public function p_dateTime() {
        if( $this->all_day ) { 
            if( $this->start->eq( $this->end )) { 
                return $this->start->format( 'Y-m-d' );
            } else {
                return $this->start->format( 'Y-m-d' ) . ' ～ ' . $this->end->format( 'Y-m-d' );
            }
        } else {
            if( $this->start_date == $this->end_date ) {
                return $this->start->format( 'Y-m-d H:i') . ' ～ '. $this->end->format( 'H:i' );                
            } else {
                if( $this->start->eq( $this->end )) { 
                    return $this->start->format( 'Y-m-d H:i' );
                } else {
                    return $this->start->format( 'Y-m-d H:i' ) . ' ～ ' . $this->end->format( 'Y-m-d H:i' );
                }
            }
        }
    }
    
    public function p_time_for_montly_form() {
        $num_day = $this->getNumDates();

        if( $num_day >= 2 ) {
            if( $this->all_day ) {
                return $this->end->format( '～ n月j日');
            } else {
                return $this->start->format( 'H:i' ) . '～' . $this->end->format( 'n月j日 H:i' );   
            }
        }
        if( $this->all_day ) {
            return $this->start->format( '終日' ); 
        } else {
            return $this->start->format( 'H:i' ) . '～' . $this->end->format( 'H:i' );   
        }
    }

    public function p_time_for_weekly_form() {
        $num_day = $this->getNumDates();
        if( $this->all_day ) {
            return "終日";
        } else {

            if( $num_day >= 2 ) {
                return $this->start->format( 'n月j日 H:i' ) . '～' . $this->end->format( 'n月j日 H:i' );
            } else {
                return $this->start->format( 'H:i' ) . '～' . $this->end->format( 'H:i' );
            }
        }
    }
    
    public function p_time_for_daily_form( ) {
        // dump( $date->format( 'Y-m-d' ) );
        // $num_day = $this->start->diffInDays( $this->end ) + 1;
        $num_day = $this->getNumDates();

        if( $num_day >= 2 ) {
            if( $this->all_day ) {
                return "終日" . $this->end->format( ' 【 ～ Y年 n月 j日 】');
            } else {
                return $this->start->format( 'H:i' ) . '～' . $this->end->format( 'H:i' ) . $this->end->format( ' 【 ～ Y年 n月 j日 】');   
            }
        }
        if( $this->all_day ) {
            return "終日";
        } else {
            return $this->start->format( 'H:i' ) . '～' . $this->end->format( 'H:i' );   
        }
    }

    public function p_time_for_index_form() {
        $num_day = $this->getNumDates();

        if( $num_day >= 2 ) {
            if( $this->all_day ) {
                return $this->start->format( 'Y年n月j日' ). $this->end->format( '～ Y年n月j日');
            } else {
                return $this->start->format( 'Y年n月j日 H:i' ) . '～' . $this->end->format( 'Y年n月j日 H:i' );   
            }
        }
        if( $this->all_day ) {
            return $this->start->format( 'Y年n月j日 終日' ); 
        } else {
            return $this->start->format( 'Y年n月j日 H:i' ) . '～' . $this->end->format( 'H:i' );   
        }
    }

    
    public function p_end_time() {

        $num_day = $this->start->diffInDays( $this->end ) + 1;

        if( $this->all_day ) {
            if( $this->start->year == $this->end->year ) {
                if( $this->start->month == $this->end->month ) {
                    return $this->end->format( 'j日' );
                } else {
                    return $this->end->format('n月j日');
                }
            } else {
                return $this->end->format( 'Y年n月j日' );
            }
        } else {
        
        }
    }
    

    public function p_time( $form_type = 'index' ) {
        
        if( $form_type == 'daily' ) {
            return $this->p_time_for_daily_form();
            
        } elseif( $form_type == 'weekly' ) {
            return $this->p_time_for_weekly_form();

        } elseif( $form_type == 'monthly' ) {
            return $this->p_time_for_montly_form();

        } elseif( $form_type == 'index' ) {
            return $this->p_time_for_index_form();
        } elseif( $form_type == 'detail' ) {
            return $this->p_time_for_index_form();
        } else {
            return $this->p_time_for_index_form();
            
        }
        
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
    
    //　所要時間を分単位で出力
    //
    public function duration() {
        if( $this->all_day ) {
            $start = new Carbon( $this->start_date );
            $end   = new Carbon( $this->end_date   );
        } else {
            $start = new Carbon( $this->start );
            $end   = new Carbon( $this->end   );
        }
        return $end->diffInMinutes( $start );
    }
    
    //　カレンダー表示用のstyle css出力を取得
    //
    public function style() {
        
        return $this->facility->style();
    }
    
}
