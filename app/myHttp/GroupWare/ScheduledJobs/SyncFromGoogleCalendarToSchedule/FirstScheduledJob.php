<?php

namespace App\myHttp\GroupWare\ScheduledJobs\SyncFromGoogleCalendarToSchedule;

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

use App\Http\Helpers\MyGoogleCalendarClient;

use App\myHttp\GroupWare\ScheduledJobs\SyncFromGoogleCalendarToSchedule\SecondListGoogleEvents;
use App\myHttp\GroupWare\Jobs\GoogleCalendar\GCalSyncCreate;
use App\myHttp\GroupWare\Jobs\GoogleCalendar\GCalSyncUpdate;
use App\myHttp\GroupWare\Jobs\GoogleCalendar\GCalSyncDelete;


class FirstScheduledJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $calprops;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {
        //
        $this->calprops = CalProp::where( 'google_sync_on', 1 )
                                 ->where( 'google_sync_bidirectional', 1 )
                                 ->get();
        
        Log::info( __FILE__ .' has been running.');
        
        if( is_debug() ) {
            $log  = " : calprops_num : ". $this->calprops->count();
            Log::debug( __METHOD__ );
            Log::debug( __METHOD__. $log );
            Log::debug( __METHOD__ );
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        //
        foreach( $this->calprops as $calprop ) {
            if( is_debug() ) {
                $log  = " : CalPropGsyncJob::dispatch";
                Log::debug( __METHOD__. $log );
                $log  = " : calprop_id : ". $calprop->id;
                $log .= " : user_id : "   . $calprop->user_id;
                $log .= " : google_calendar_id : ". $calprop->google_calendar_id;
                Log::debug( __METHOD__. $log );
                Log::debug( __METHOD__ );
            }
            
            SecondListGoogleEvents::dispatch( $calprop );
        }    
    }
}
