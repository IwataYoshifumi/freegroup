<?php

namespace App\myHttp\GroupWare\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use DB;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\ReportProp;


class Report extends Model {

    // use SoftDeletes;
    
    protected $fillable = [
        'user_id', 'schedule_id',
        'title', 'place', 'start_time', 'end_time', 'memo', 
    ];

    // protected $hidden = [];

    protected $dates = [ 'start', 'end' ];
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  リレーションの定義
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    //　作成者
    public function user() {
        return $this->belongsTo( User::class );
    }
    
    public function creator() {
        return $this->user();
    }
    
    public function updator() {
        return $this->belongsTo( User::class, 'updator_id' );
    }
    
    public function users() {
        return $this->morphedByMany( User::class, 'reportable' );
    }
    
    public function attendees() {
        return $this->users();
    }

    public function customers() {
        return $this->morphedByMany( Customer::class, 'reportable' );
    }
    
    public function schedules() {
        return $this->morphedByMany( Schedule::class, 'reportable' );
    }

    public function files() {
        return $this->morphToMany( MyFile::class, 'fileable' );
    }
    
    public function report_list() {
        return $this->belongsTo( ReportList::class );
    }
    
    public function report_props() {
        return $this->hasMany( ReportProp::class );
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  値取得メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public function schedule() {
        return $this->schedules->first();
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  定数取得メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    public static function getPermissions() {
        return config( 'groupware.report.permissions' );
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
            $this->report_list->canRead( $user->id ) ) {
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
    //  表示用関数
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
    
    



    


}
