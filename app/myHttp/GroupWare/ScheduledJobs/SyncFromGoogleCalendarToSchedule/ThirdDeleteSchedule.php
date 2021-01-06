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

use App\myHttp\GroupWare\Models\Actions\ScheduleAction;

use App\Http\Helpers\MyGoogleCalendarClient;

use App\myHttp\GroupWare\ScheduledJobs\SyncFromGoogleCalendarToSchedule\MyGoogleEventClass;
use App\myHttp\GroupWare\Jobs\GsyncScheduleUnSyncJob;

class ThirdDeleteSchedule implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $schedule;
    public $calprop;
    public $gcal_sync;
    public $user;
    public $file;  // google private key file 

    /**
     * Create a new job instance.
     *
     * @return void
     */
     
    public function __construct( GCalSync $gcal_sync ) {
        
        $this->gcal_sync    = $gcal_sync;
        $this->schedule     = $gcal_sync->schedule;
        $this->user         = $gcal_sync->calprop->user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        //
        if( ! $this->isOKInputs() ) { return $this->die(); }

        $schedule = $this->schedule;

        GsyncScheduleUnSyncJob::dispatch( $schedule, $schedule->calendar );

        // スケジュール削除のイベント
        //
        // event( new ScheduleDeletedEvent( $schedule ));

        $return = ScheduleAction::deletes( $schedule );

    }

    //　入力チェック
    //
    public function isOKInputs() {
        $user     = $this->user;
        $schedule = $this->schedule;
        
        if( ! $user->can( 'delete', $schedule )) { return false; }
        
        return true;
    }
    
    public function die() {
        return false;
        // die( __METHOD__ . $log );
    }
}        