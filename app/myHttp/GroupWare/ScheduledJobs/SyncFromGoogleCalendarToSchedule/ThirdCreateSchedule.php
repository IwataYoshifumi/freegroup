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
use App\myHttp\GroupWare\Jobs\GsyncScheduleCreatedJob;

class ThirdCreateSchedule implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $schedule;
    public $google_event;
    public $calendar;
    public $calprop;
    public $creator; // $user

    /**
     * Create a new job instance.
     *
     * @return void
     */
     
    public function __construct( CalProp $calprop, MyGoogleEventClass $google_event ) {
        $this->calprop      = $calprop;
        $this->calendar     = $calprop->calendar;
        $this->creator      = $calprop->user;
        $this->google_event = $google_event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        //
        if( ! $this->isOKInputs() ) { return $this->die(); }

        $google_event = $this->google_event;
        $calendar     = $this->calendar;
        $calprop      = $this->calprop;
        $user         = $this->creator;

        //　$google カレンダーに追加された予定を反映
        //
        $schedule = new Schedule;
        $schedule->user_id     = $user->id;
        $schedule->updator_id  = $user->id;
        $schedule->calendar_id = $calendar->id;
        $schedule->permission  = $calprop->default_permission;
        $schedule->name        = $google_event->summary;
        $schedule->place       = $google_event->location;
        $schedule->start_date  = $google_event->start_date;
        $schedule->end_date    = $google_event->end_date;
        $schedule->start       = $google_event->start_time;
        $schedule->end         = $google_event->end_time;
        $schedule->all_day     = $google_event->all_day;
        $schedule->memo        = $google_event->description;
        $schedule->save();
        
        $this->schedule = $schedule;
        
        // if_debug( $schedule );

        //　他のGoogleカレンダーにも同期
        //
        GsyncScheduleCreatedJob::dispatch( $schedule );

    }

    //　入力チェック
    //
    public function isOKInputs() {
        if( $this->calendar->canNotWrite( $this->creator )) { return false; }
        if( is_null( $this->calprop )) { return false; }
        
        return true;
    }
    
    public function die() {
        return false;
        // die( __METHOD__ . $log );
    }
}        