<?php

namespace App\myHttp\GroupWare\ScheduledJobs\SyncFromGoogleCalendarToSchedule;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\GCalSync;
use App\myHttp\GroupWare\Models\File as MyFile;

use App\Http\Helpers\MyGoogleCalendarClient;

use App\myHttp\GroupWare\ScheduledJobs\SyncFromGoogleCalendarToSchedule\MyGoogleEventClass;
use App\myHttp\GroupWare\Jobs\GsyncScheduleUpdatedJob;

class ThirdUpdateSchedule implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $schedule;
    public $gcal_sync;
    public $google_event;

    public $user;
    public $calprop;

    /**
     * Create a new job instance.
     *
     * @return void
     */
     
    public function __construct( Schedule $schedule, Gcalsync $gcal_sync, MyGoogleEventClass $google_event ) {

        $this->schedule     = $schedule;
        $this->gcal_sync    = $gcal_sync;
        $this->google_event = $google_event;
        
        $this->calprop      = $gcal_sync->calprop;
        $this->user         = $this->calprop->user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        //
        
        if( ! $this->isOKInputs() ) { return $this->die(); }
        
        $schedule     = $this->schedule;
        $google_event = $this->google_event;
        $gcal_sync    = $this->gcal_sync;
        $calprop      = $this->calprop;
        $user         = $this->user;

        //　$google カレンダーの変更をスケジュールに反映し、
        //  他のGoogleカレンダーにも同期する。

        $schedule->name       = $google_event->summary;
        $schedule->place      = $google_event->location;
        $schedule->memo       = $google_event->description;

        $schedule->start_date = $google_event->start_date;
        $schedule->end_date   = $google_event->end_date;
        $schedule->start      = $google_event->start_time;
        $schedule->end        = $google_event->end_time;
        $schedule->all_day    = $google_event->all_day;
        $schedule->updator_id = $user->id;
        $schedule->save();
        
        $gcal_sync->google_etag = $google_event->etag;
        $gcal_sync->google_synced_at = $google_event->updated;
        $gcal_sync->save();
        
        // GsyncScheduleUpdatedJob::dispatch( $schedule, $gcal_sync );
        GsyncScheduleUpdatedJob::dispatch( $schedule );

    }

    //　入力チェック
    //
    public function isOKInputs() {

        $schedule = $this->schedule;
        $user     = $this->user;
        if( $this->google_event->status != 'confirmed' ) { return false; }
        if( ! $user->can( 'update',  $schedule ))        { return false; }

        return true;
    }
    
    public function die() {
        return false;
        // die( __METHOD__ . $log );
    }
}        