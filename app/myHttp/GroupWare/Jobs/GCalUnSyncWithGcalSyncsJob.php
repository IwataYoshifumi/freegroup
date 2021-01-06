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

class GCalUnSyncWithGcalSyncsJob implements ShouldQueue
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
        $this->gcal_syncs = $gcal_syncs->load( 'calprop', 'calprop.files');
        
        if( is_debug() ) {
            $log = " : gcal_syncs_num : ". $gcal_syncs->count();
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
        $gcal_syncs = $this->gcal_syncs;
        if( is_debug() ) {
            $log = " : gcal_syncs_num : ". $gcal_syncs->count();
            Log::debug( __METHOD__ . $log );
        }

        //　Google 同期設定のある人だけ同期処理
        //
        foreach( $gcal_syncs as $gcal_sync ) {

            $calprop         = $gcal_sync->calprop;
            $google_event_id = $gcal_sync->google_event_id;

            if( is_debug() ) {
                $log  = " : gcal_sync_id : " . $gcal_sync->id;
                $log .= " : calprop_id   : " . $calprop->id;
                $log .= " : user_id : "      . $calprop->user_id;
                $log .= " : schedule_id  : " . $gcal_sync->schedule_id;
                $log .= " : calendar_id  : " . $calprop->calendar_id;
                $log .= " : g_event_id   : " . $google_event_id;
                Log::debug( __METHOD__ . $log );
            }
            GCalSyncDelete::dispatch( $gcal_sync );                
        }
        
    }
}
