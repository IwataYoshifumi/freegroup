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

use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\AccessList;

use App\myHttp\GroupWare\Models\Customer;
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\File as MyFile;

class GCalSync extends Model {

    // use SoftDeletes;
    protected $table = 'gcal_syncs';
    
    protected $fillable = [
        'schedule_id',
        'calprop_id',
        
        'google_event_id', 
        'google_synced_at', 
        'google_etag',
    ];
    
    protected $dates = [ 'google_synced_at', 'created_at', 'updated_at' ];
    
    // protected $hidden = [];

    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  リレーションの定義
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    
    public function schedule() {
        return $this->belongsTo( Schedule::class, 'schedule_id' );
    }
    
    public function calprop() {
        return $this->belongsTo( CalProp::class, 'calprop_id' );
    }
    
    public function user() {
        return $this->calprop->user;
    }
    
    public function calendar() {
        return $this->calprop->calendar;
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////
    //
    //  コレクション取得メソッド
    //
    /////////////////////////////////////////////////////////////////////////////////////////////
    
    public static function getBySchedule( $schedule ) {
        $schedule = ( $schedule instanceof Schedule ) ? $schedule : Schedule::find( $schedule );
        $calendar = $schedule->calendar;
        
        // $gcal_syncs = GCalSync::whereHas( 'calprop', function( $query ) use( $schedule, $calendar ) { $query->where( 'calendar_id', $calendar->id ); })
        //                       ->where( 'schedule_id', $schedule->id )->get();
        $gcal_syncs = GCalSync::where( 'schedule_id', $schedule->id )->get();        
        
        return $gcal_syncs;
    }

    public static function getWhereSchedule( $schedule ) {
        return self::getBySchedule( $schedule );
    }
    
    public static function getByScheduleAndCalProp( $schedule, $calprop ) {
        $schedule = ( $schedule instanceof Schedule ) ? $schedule : Schedule::find( $schedule );
        $calprop  = ( $calprop  instanceof CalProp  ) ? $calprop  : CalProp::find( $calprop   );
        
        return GCalSync::where( 'schedule_id', $schedule->id )->where( 'calprop_id', $calprop->id )->first();
    }
    

}
