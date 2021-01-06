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

use App\myHttp\GroupWare\Jobs\GoogleCalendar\GCalSyncCreate;

use App\Http\Helpers\MyGoogleCalendarClient;

class GCalSyncUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $schedule;
    public $calprop;
    public $gcal_sync;
    public $gcal_syncs;
    public $user;
    public $file;  // google private key file 

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Schedule $schedule, CalProp $calprop ) {

        $this->schedule  = $schedule->load( 'user', 'calendar', 'attendees' );
        $this->calprop   = $calprop->load(  'user', 'calendar' );
        $this->gcal_syncs = GCalSync::where( 'schedule_id', $schedule->id )->where( 'calprop_id', $calprop->id )->get();
        $this->gcal_sync = $this->gcal_syncs->first();
        $this->user      = $calprop->user;
        $this->file      = $calprop->google_private_key_file();

        $log =  " : schedule_id : "  . $schedule->id;
        $log .= " : calprop_id  : "  . $calprop->id;
        $log .= " : gcal_sync_id  : ". op( $this->gcal_sync )->id;
        $log .= " : old_etag  : "    . op( $this->gcal_sync )->google_etag;

        $log .= " : user_id : ". $this->user->id;
        $log .= " : file_id : ". $this->file->id . " : file_name : ". $this->file->file_name ;
        Log::info( __METHOD__ . $log );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        //
        if( ! $this->isOKInputs() ) { throw new Exception( __METHOD__ ); }

        $schedule  = $this->schedule;
        $calprop   = $this->calprop;
        $gcal_sync = $this->gcal_sync;
        $file      = $this->file;
        
        $google_id          = $calprop->google_id;
        $google_calendar_id = $calprop->google_calendar_id;

        // GCalSyncのデータ不整合対策
        //
        if( is_null( $gcal_sync )) { 
            return GCalSyncCreate::dispatch( $schedule, $calprop ); // もし GCalSync がなければ Create
            
        } elseif( $this->gcal_syncs->count() > 1 ) {                
            $this->gcal_syncs->toQuery()->delete();                 //　もし複数のGCalSyncがあれば、一旦全て削除して、Create
            return GCalSyncCreate::dispatch( $schedule, $calprop ); 
        }

        //  Google Calendar Update 同期処理
        //
        try {
            $client = new MyGoogleCalendarClient( $calprop );
            $event = $client->update( $schedule, $gcal_sync );
            if_debug( $event );        
        } catch( Exception $e ) {
            Log::error( __METHOD__  );
            if_debug( __METHOD__, $e );
            return false;
        }

        //  GCalSync DBへのGoogle同期情報のインポート
        //
        $gcal_sync = DB::transaction( function() use ( $event, $schedule, $calprop, $gcal_sync ) {
            $gcal_sync->google_event_id  = $event->id;
            $gcal_sync->google_etag      = $event->etag;
            $gcal_sync->google_synced_at = $event->updated;
            $gcal_sync->save();
            return $gcal_sync;
        }); 
    }

    //　入力チェック
    //
    public function isOKInputs() {

        if( is_debug() ) {
            $log  = " : calendar_id  : ". $this->schedule->calendar->id;
            $log .= " : schedule_id  : ". $this->schedule->id;
            $log .= " : calprop_id  : " . $this->calprop->id;
            $log .= " : gcal_sync_id  : ". ( ! empty( $this->gcal_sync )) ? op( $this->gcal_sync )->id : 'NULL';
            $log .= " : schedule_creator_id : ". $this->schedule->creator->id;
            $log .= " : calprop_user_id : "    . $this->calprop->user->id;
            $log .= " : sync_level : "         . $this->calprop->google_sync_level;
            Log::debug( __METHOD__ . $log );
        }
    
        if( empty( $this->gcal_sync )) {
            Log::warning( __METHOD__ . " warning : no gcal_sync");
            //return false;
        }
    
        if( empty( $this->calprop->google_calendar_id )) {
            Log::error( __METHOD__ . " error1 1");
            return false;
        }
    
        if( ! $this->calprop->google_sync_on ) {
            Log::error( __METHOD__ . " error 2");
            return false;
        }
        if( empty( $this->file )) {
            Log::error( __METHOD__ . " error 3");
            return false;
        }
        return true;
    }
    
    public function die() {
        $log = " : schedule_id : " . $this->schedule->id;
        $log .= ": calprop_id : "  . $this->calprop->id;
        Log::error( __METHOD__. $log );
        return false;
        
        // die( __METHOD__ . $log );
    }
}        