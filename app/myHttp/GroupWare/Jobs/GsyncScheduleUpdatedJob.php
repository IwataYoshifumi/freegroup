<?php

namespace App\myHttp\GroupWare\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Arr;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\GCalSync;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\Jobs\GoogleCalendar\GCalSyncUpdate;
use App\myHttp\GroupWare\Jobs\GoogleCalendar\GCalSyncDelete;


class GsyncScheduleUpdatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $schedule;
    public $except_gcalsync;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Schedule $schedule, $except_gcalsync = null ) {
        //
        $this->schedule        = $schedule->load( 'user', 'creator', 'calendar', 'attendees' );
        $this->except_gcalsync = null;
        
        if( ! is_null( $except_gcalsync )) {
            if( $except_gcalsync instanceof GCalSync ) {
                $this->except_gcalsync = $except_gcalsync;
            } else {
                $this->except_gcalsync = GCalSync::find( $except_gcalsync );
            }
            
        }
    }
    
    

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        //
        $schedule   = $this->schedule;
        $calendar   = $schedule->calendar;
        $calprops   = $calendar->calprops()->where( 'google_sync_on', 1 )->get();
        
        if( is_debug() ) {
            $log  = " : schedule_id  : ". $schedule->id;
            $log .= " : calendar_id  : ". $calendar->id;
            $log .= " : calener_name : ". $calendar->name;
            $log .= " : calprops_num : ". $calprops->count();
            Log::debug( __METHOD__ . $log );
        }

        //　Google 同期設定のある人だけ同期処理
        //
        foreach( $calprops as $calprop ) {

            $user_id    = $calprop->user_id;

            if( op( $this->except_gcalsync )->calprop_id == $calprop->id ) { continue; }

            if( is_debug() ) {
                $log  = " : calprop_id  : " . $calprop->id;
                $log .= " : user_id : ".      $user_id;
                $log .= " : sync_level : ".   $calprop->google_sync_level;
                Log::debug( __METHOD__ . $log );
            }
            
            if( $schedule->user->id == $user_id ) {
                //
                // 自分の作成したものだけ同期
                //
                if( is_debug() ) { Log::debug( __METHOD__. " : dispach( GCalSyncUpdate ) : mine " ); }

                GCalSyncUpdate::dispatch( $schedule, $calprop );                

            } elseif( $calprop->google_sync_level == 'attend' ) {
                if( $schedule->isAttendee( $user_id ) ) {
                    //
                    //　関連付け（attend）されたものを同期
                    //
                    if( is_debug() ) { Log::debug( __METHOD__.  " : dispach( GCalSyncUpdate ) : attend "); }
    
                    GCalSyncUpdate::dispatch( $schedule, $calprop );

                } else {
                    $gcal_sync = GCalSync::getByScheduleAndCalProp( $schedule, $calprop );
                    if( $gcal_sync ) {
                        GCalSyncDelete::dispatch( $gcal_sync );
                    }
                }                

            } elseif( $calprop->google_sync_level == 'all' ) {
                //
                //　カレンダー内のスケジュール全て同期
                //
                if( is_debug() ) { Log::debug( __METHOD__. " : dispach( GCalSyncUpdate ) : all " ); }

                GCalSyncUpdate::dispatch( $schedule, $calprop );                

            } else {
                $log  = " : No Dispach";
                $log  = " : schedule_id  : ". $schedule->id;
                $log .= " : calendar_id  : ". $calendar->id;
                $log .= " : calprop_id  : " . $calprop->id;
                $log .= " : user_id : ".      $calprop->user_id;
                $log .= " : sync_level : ".   $calprop->google_sync_level;
                Log::warning( __METHOD__. $log );
                continue;
            }
            
        }
    }
    
    private function isDataOK( Schedule $schedule, CalProp $calprop, GCalSync $gcal_sync ) {
        if( $schedule->calendar_id === $calprop->calendar_id    and 
            $schedule->id          === $gcal_sync->schedule_id  and 
            $calprop->id           === $gcal_sync->calendar_id ) {
                return true;
        
            }        
        return false;
        
    }
    
}
