<?php

namespace App\myHttp\GroupWare\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\myHttp\GroupWare\Jobs\GoogleCalendar\GCalSyncCreate;

class GsyncScheduleCreatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $schedule;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Schedule $schedule ) {
        $this->schedule = $schedule->load( 'user', 'creator', 'calendar', 'attendees' );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        //

        $schedule = $this->schedule;
        $calendar = $schedule->calendar;
        $calprops = $calendar->calprops()->where( 'google_sync_on', 1 )->get();

        if( is_debug() ) {
            $log  = " : schedule_id  : ". $schedule->id;
            $log .= " : calendar_id  : ". $calendar->id;
            $log .= " : calener_name : ". $calendar->name;
            Log::debug( __METHOD__ . $log );
        }
 
        //　Google 同期設定のある人だけ同期処理
        //
        foreach( $calprops as $calprop ) {

            $user_id = $calprop->user_id;

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
                if( is_debug() ) { Log::debug( __METHOD__. " : dispach( GCalSynCreate ) : mine " ); }
                
                GCalSyncCreate::dispatch( $schedule, $calprop );                

            } elseif( $calprop->google_sync_level == 'attend' and $schedule->isAttendee( $user_id ) ) {
                //
                // 関連付け（attend）されたものを同期
                //
                if( is_debug() ) { Log::debug( __METHOD__. " : dispach( GCalSynCreate ) : attend " ); }
                
                GCalSyncCreate::dispatch( $schedule, $calprop );                

            } elseif( $calprop->google_sync_level == 'all' ) {
                //
                //　カレンダー内のスケジュール全て同期
                //
                if( is_debug() ) { Log::debug( __METHOD__. " : dispach( GCalSynCreate ) : all " ); }
 
                GCalSyncCreate::dispatch( $schedule, $calprop );                

            } else {
                $log  = " : No Dispach";
                $log  = " : schedule_id  : ". $schedule->id;
                $log .= " : calendar_id  : ". $calendar->id;
                $log .= " : calprop_id  : " . $calprop->id;
                $log .= " : user_id : ".      $calprop->user_id;
                $log .= " : sync_level : ".   $calprop->google_sync_level;
                Log::warning( __METHOD__. $log );
            }
        }
        
    }
}