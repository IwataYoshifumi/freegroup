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
use App\myHttp\GroupWare\Jobs\GoogleCalendar\GCalSyncCreate;
use App\myHttp\GroupWare\Jobs\GoogleCalendar\GCalSyncUpdate;
use App\myHttp\GroupWare\Jobs\GoogleCalendar\GCalSyncDelete;

use App\myHttp\GroupWare\ScheduledJobs\SyncFromGoogleCalendarToSchedule\MyGoogleEventClass;
use App\myHttp\GroupWare\ScheduledJobs\SyncFromGoogleCalendarToSchedule\ThirdCreateSchedule;
use App\myHttp\GroupWare\ScheduledJobs\SyncFromGoogleCalendarToSchedule\ThirdUpdatechedule;
use App\myHttp\GroupWare\ScheduledJobs\SyncFromGoogleCalendarToSchedule\ThirdDeleteSchedule;
   
class SecondListGoogleEvents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $calprop;
    public $calendar;
    public $user;
    
    private $google_client;
    
    private $google_event_lists;
    private $google_events;
    private $updated_gcal_syncs;
    private $deleted_gcal_syncs;
    private $created_google_events;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( CalProp $calprop ) {
        //
        $this->calprop  = $calprop;
        $this->calendar = $calprop->calendar;
        $this->user     = User::find( $calprop->user_id );

        $this->debug_log_3( $this, __METHOD__ ); 
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        //
        if( ! $this->checkSyncOrNot() ) { return false; }

        // Google カレンダーを検索（Event listメソッドを実行）・新規作成・更新・削除されたGoogle Event を取得
        //
        $this->getList();

        //　スケジュールを更新
        //
        $this->updateSchedules();
        
        //　スケジュールを削除
        //
        $this->deleteSchedules();
        
        //　スケジュールを新規追加
        //
        $this->createSchedules();
        
        $this->calprop->google_synced_at = now();
        $this->calprop->save();
    }

    // Googleカレンダーで追加された予定をFreeGroup側へ反映
    //
    public function createSchedules() {

        $client   = $this->google_client;
        $user     = $this->user;
        $calprop  = $this->calprop;
        $calendar = $this->calendar;

        $google_calendar_id = $calprop->google_calendar_id;
        
        //　カレンダーへのアクセス権限があるか確認
        //
        if( $calendar->canNotWrite( $user )) { if_debug( __METHOD__, 'canNot Write Calendar' ); return false; }
        
        foreach( $this->created_google_events as $google_event ) {

            $return = $client->delete( $google_calendar_id, $google_event->id );
            
            ThirdCreateSchedule::dispatch( $calprop, $google_event ); 

        }
    }

    // Googleカレンダーで変更された予定をFreeGroup側に反映
    //
    public function updateSchedules() {
    
        $user = $this->user;
    
        foreach( $this->updated_gcal_syncs as $gcal_sync ) {
            $google_event = $this->google_events[$gcal_sync->google_event_id];

            // Etagが異なることと、更新日時を確認
            //
            if( strcmp( $gcal_sync->google_etag, $google_event->etag ) !== 0 and 
                $google_event->updated->gt( $gcal_sync->google_synced_at )) {
                    
                $schedule = Schedule::find( $gcal_sync->schedule_id );

                if( $user->can( 'update', $schedule ) ) {
                    // if_debug( "__METHOD__ , gcal_sync $gcal_sync->id " );
                    ThirdUpdateSchedule::dispatch( $schedule, $gcal_sync, $google_event ); 
                }
            }
        }
    }

    // Googleカレンダーで削除された予定をFreeGroup側へ反映
    //
    public function deleteSchedules() {

        $user = $this->user;
        
        foreach( $this->deleted_gcal_syncs as $gcal_sync ) {
            $schedule = $gcal_sync->schedule;
            // $user     = $gcal_sync->calprop->user;
            
            if( $user->can( 'delete', $schedule )) {
                ThirdDeleteSchedule::dispatch( $gcal_sync );                
            }
        }
    }

    // Google 同期の対象カレンダーか確認
    //
    public function checkSyncOrNot() {
        $calprop  = $this->calprop;
        $calendar = $calprop->calendar; 
        $user     = $calprop->user;
        
        if( $calprop->google_sync_on            == 0 ) { return false; }
        if( $calprop->google_sync_check         == 0 ) { return false; }
        if( $calprop->google_sync_bidirectional == 0 ) { return false; }
        if( $calendar->canNotWrite( $user ))           { return false; }

        return true;        
    }
    
    // GoogleカレンダーのListメソッドを実行して、更新・削除されたGoogle イベントを取得する
    // レスポンス（Googleイベントの配列、更新されたイベントＩＤの配列、削除されたイベントＩＤの配列
    //
    public function getList() {

        // $calprop = $that->calprop;
        $calprop = $this->calprop;
        $user    = $calprop->user;
        $calendar= $calprop->calendar;

        //
        // Google カレンダーの検索条件を作成
        //
        $today = Carbon::today();
        $timeMax = $today->copy()->addDays( $calprop->google_sync_span );
        $timeMin = $today->copy()->subDays( $calprop->google_sync_span );
        $updatedMin = ( $calprop->google_synced_at ) ? $calprop->google_synced_at : null;
        // if_debug( 'calprop->google_synced_at', $updatedMin );

        $optParams = array(
          'timeMax' => $timeMax->toAtomString(),
          'timeMin' => $timeMin->toAtomString(),
        );
        if( $updatedMin ) {
            $optParams['updatedMin'] = $updatedMin->subMinutes( 3 )->toAtomString();
            // $optParams['updatedMin'] = $today->subDays(3)->toAtomString();
        } else {
            $optParams['updatedMin'] = $today->subDays(5)->toAtomString();
        }
        if( is_debug() ) { if_debug( 'Google Event List', $optParams ); }
        $this->debug_log_2( $optParams, $updatedMin, __METHOD__ );

        //
        // Google カレンダーを検索（listメソッド実行　イベントリスト取得）
        //
        $client = new MyGoogleCalendarClient( $calprop );
        $google_event_lists = $client->list( $optParams );
        
        if( is_debug() ) { if_debug( $google_event_lists ); }

        $google_events = [];
        $updated_event_ids = [];  // updatedMin 以降に更新・追加（新規作成）された google event id の配列
        $deleted_event_ids = [];  // updatedMin 以降に削除された google event id の配列
        $created_event_ids = [];  // updatedMin 以降に追加された google event id の配列

        // google event id をキーに配列を作成（ $google_events )
        //
        foreach( $google_event_lists as $i => $google_event ) {
            $this->debug_log_1( $calprop, $google_event , __METHOD__);

            if( $google_event->status == 'confirmed' ) {
                $updated_event_ids[$google_event->id] = $google_event->id;

            } elseif( $google_event->status == 'cancelled' ) {
                $deleted_event_ids[$google_event->id] = $google_event->id;
            }
            $google_events[ $google_event->id ] = new MyGoogleEventClass( $google_event );
        }
        if( is_debug() ) { if_debug( 'google events', $google_events, 'updated_event_ids', $updated_event_ids, 'deleted_event_ids', $deleted_event_ids );  }

        //
        // 更新・削除対象のスケジュール（ Gcalsync）を検索
        //
        $updated_gcal_syncs = GCalSync::whereIn( 'google_event_id', $updated_event_ids )->get();
        $deleted_gcal_syncs = GCalSync::whereIn( 'google_event_id', $deleted_event_ids )->get();

        // 新規作成されたGoogle イベントを抽出
        //
        $updated_event_ids     = $updated_gcal_syncs->pluck( 'id', 'google_event_id')->toArray();
        $created_google_events = array_diff_key( $google_events, $updated_event_ids, $deleted_event_ids );
        if( is_debug() ) { if_debug( 'created_google_events', $created_google_events ,'UPDATED GCalSyncs', $updated_gcal_syncs, 'DELETED GCalSyncs', $deleted_gcal_syncs ); }

        $this->google_client         = $client;
        $this->google_event_lists    = $google_event_lists;
        $this->google_events         = $google_events;
        $this->updated_gcal_syncs    = $updated_gcal_syncs;
        $this->deleted_gcal_syncs    = $deleted_gcal_syncs;
        $this->created_google_events = $created_google_events;

        return true;        
    }
     
    private function debug_log_1( $calprop, $google_event, $method ) {
        if( is_debug() ) {
            $log  = " : calprop_id : ". $calprop->id;
            Log::debug( $method. $log );
            $log = " : google_calendar_id : ". $calprop->google_calendar_id;
            Log::debug( $method. $log );
            $log = " : google_event_id : "   . $google_event->id;
            Log::debug( $method. $log );
            $log = " : google_etag : "       . $google_event->etag;
            Log::debug( $method. $log );
            $log = " : google_updated : "    . $google_event->updated;
            Log::debug( $method. $log );
            $log = " : google_status : "     . $google_event->status;
            Log::debug( $method. $log );
            $log = " : google_summary : "    . $google_event->summary;
            Log::debug( $method. $log );
            Log::debug( '' );
        }
    }
    
    private function debug_log_2( $optParams, $updatedMin, $method ) {
        
        if( is_debug() ) {
            Log::debug( $method. " : timeMax    : ". $optParams['timeMax']);
            Log::debug( $method. " : timeMin    : ". $optParams['timeMin']);
            if( $updatedMin ) {
            Log::debug( $method. " : updatedMin : ". $optParams['timeMax']);
            }
            Log::debug( '' );
        }
        
    }
    
    private function debug_log_3( $that, $method ) {
        if( is_debug() ) {
            Log::debug( $method. '---------------------------------------------------- START');
            $log  = " : calprop_id : "        . $that->calprop->id;
            Log::debug( $method. $log );
            $log  = " : google_calendar_id : ". $that->calprop->google_calendar_id;
            Log::debug( $method. $log );
            $log  = " : user_id : "           . $that->calprop->user_id;
            Log::debug( $method. $log );
        }
    } 
     
        
}

