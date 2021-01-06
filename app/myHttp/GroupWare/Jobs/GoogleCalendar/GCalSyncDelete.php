<?php

namespace App\myHttp\GroupWare\Jobs\GoogleCalendar;

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

class GCalSyncDelete implements ShouldQueue
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

        $this->gcal_sync = $gcal_sync;
        $this->calprop   = $gcal_sync->calprop;
        $this->file      = $gcal_sync->calprop->google_private_key_file();

        if( is_debug() ) {
            $log  = " : gcal_sync_id : ". $gcal_sync->id;
            $log .= " : schedule_id : " . $gcal_sync->schedule_id;
            $log .= " : calprop_id  : " . $this->calprop->id;
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
        if( ! $this->isOKInputs() ) { return $this->die(); }

        $gcal_sync = $this->gcal_sync;
        $calprop   = $this->calprop;

        //  GCalSync DBを削除
        //
        $return = $gcal_sync->delete();

        //  Google Calendar 同期処理
        //
        try {
            $client = new MyGoogleCalendarClient( $calprop );
            $return = $client->deleteWithGcalSync( $gcal_sync );
            
        } catch( Google_Service_Exception $e ) {
            Log::error( __METHOD__  );
            throw new Exception( $e );
        }
    }

    //　入力チェック
    //
    public function isOKInputs() {

        if( empty( $this->calprop->google_calendar_id )) {
            Log::error( __METHOD__ . " error1 1 : No Google Calendar ID");
            return false;
        }
        if( ! $this->calprop->google_sync_on ) {
            Log::error( __METHOD__ . " error 2 : Allready not Google Sync ");
            return false;
        }
        if( empty( $this->file )) {
            $log = " : file_id : " . op( $this->file )->id;
            Log::error( __METHOD__ . " error no Google Auth File");
            return false;
        }
        return true;
    }
    
    public function die() {
        
        $log  = " : gcal_sync_id : ". $this->gcal_sync->id;
        $log .= " : schedule_id : " . $this->gcal_sync->schedule_id;
        $log .= " : calprop_id  : " . $this->calprop->id;
        Log::error( __METHOD__. $log );
        return false;
    }
}        