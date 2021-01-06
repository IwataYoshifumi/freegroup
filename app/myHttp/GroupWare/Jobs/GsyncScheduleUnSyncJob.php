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

use App\myHttp\GroupWare\Jobs\GoogleCalendar\GCalSyncDelete;

class GsyncScheduleUnSyncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $schedule;
    public $calendar;
    public $gcal_syncs;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Schedule $schedule, Calendar $calendar ) {
        //
        $this->schedule   = $schedule;
        $this->calendar   = $calendar;

        $subquery   = CalProp::where( 'calendar_id', $calendar->id )->select( 'id' );
        $gcal_syncs = GCalSync::where(   'schedule_id', $schedule->id )
                              ->whereIn( 'calprop_id',  $subquery )->with( 'calprop', 'calprop.files' )->get();
        $this->gcal_syncs = $gcal_syncs;
        
        if( is_debug() ) {
            $log  = " : schedule_id    : ". $schedule->id;
            $log .= " : calendar_id    : ". $calendar->id;
            $log .= " : gcal_syncs_num : ". $gcal_syncs->count();
            
            Log::debug( __METHOD__ . $log );
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
        $calendar   = $this->calendar;
        $gcal_syncs = $this->gcal_syncs;

        if( is_debug() ) {
            $log  = " : schedule_id    : ". $schedule->id;
            $log .= " : calendar_id    : ". $calendar->id;
            Log::debug( __METHOD__ . $log );
        }

        //　Google 同期設定のある人だけ同期処理
        //
        // dump( $gcal_syncs );
        foreach( $gcal_syncs as $gcal_sync ) {

            $calprop         = $gcal_sync->calprop;

            if( is_debug() ) {
                $log  = " : gcal_sync_id : " . $gcal_sync->id;
                $log .= " : calprop_id   : " . $calprop->id;
                $log .= " : user_id : "      . $calprop->user_id;
                Log::debug( __METHOD__ . $log );
            }

            GCalSyncDelete::dispatch( $gcal_sync );                
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
