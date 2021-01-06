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

class GSyncWithCalPropJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $gcal_syncs;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $gcal_syncs ) {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        //
    
        
    }
}
