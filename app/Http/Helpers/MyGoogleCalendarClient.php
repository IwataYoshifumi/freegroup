<?php 

namespace App\Http\Helpers;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Exception;
use LogicException;

use Carbon\Carbon;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Tasks;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Exception;
use Spatie\GoogleCalendar\Event;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\GCalSync;
use App\myHttp\GroupWare\Models\File as MyFile;

class MyGoogleCalendarClient {

    public $schedule;   // Schedule::class
    public $calprop;    // Calprop::class

    public $google_client;     // Google_Client 
    public $google_service;    // Google_Service_Calendar

    public $event;      // Google_Calendar_Event
    public $old_event;  // Google_Calendar_Event
    
    public $inputs;  // Calendar Data [ start, end, summary, location, description, url ... ]

    public function __construct( Calprop $calprop ) {

        $this->calprop = $calprop;

        // Googleカレンダーへ接続( called from init** )
        //
        try {
            $file = $calprop->google_private_key_file();
            $client = new Google_Client();
            $client->setApplicationName('Network Tokai Groupware it is FreeGroup');
            $client->setScopes( Google_Service_Calendar::CALENDAR );
            $client->setAuthConfig( storage_path( 'app/'. $file->path ));
            
            $this->google_service = new Google_Service_Calendar( $client ); 
            $this->google_client = $client;

        } catch( Google_Service_Exception $e ) {
            $log  = " : Google_Service_Exception : ";
            Log::error( __METHOD__ . $log );
            throw new Exception(  __METHOD__ );
        }
        // if_debug( $this );
    }
    
    public function create( Schedule $schedule ) {
        
        $google_event       = self::getGoogleEventInstance( $schedule, $this->calprop->user );
        $google_calendar_id = $this->calprop->google_calendar_id;
        
        return $this->google_service->events->insert( $google_calendar_id, $google_event );
    }
    
    public function update( Schedule $schedule, GCalSync $gcal_sync ) {

        $google_event   = self::getGoogleEventInstance( $schedule, $this->calprop->user );
        $google_calendar_id = $this->calprop->google_calendar_id;
        $google_event_id    = $gcal_sync->google_event_id;
        
        return $this->google_service->events->update( $google_calendar_id, $google_event_id, $google_event );
    }
    

    public function delete( $google_calendar_id, $google_event_id ) {

        $event = $this->google_service->events->get( $google_calendar_id, $google_event_id );

        // すでに削除されているイベントを削除しようとすると 404レスポンスを返すのでそのチェック
        // 
        if( $event->status !== 'cancelled' ) {
            $this->google_service->events->delete( $google_calendar_id, $google_event_id );
        } 
        return true;
    }

    public function deleteWithGcalSync( GCalSync $gcal_sync ) {

        $google_event_id    = $gcal_sync->google_event_id;
        $google_calendar_id = $gcal_sync->calprop->google_calendar_id;
        
        return $this->delete( $google_calendar_id, $google_event_id );
    }
    


    //　optParams はEvents::listメソッドに渡す検索条件 ( https://developers.google.com/calendar/v3/reference/events/list )
    //　キーは下記の通り
    //　
    //  timeMax   2011-06-03T10:00:00-07:00, 2011-06-03T10:00:00Z.
    //  timeMin   2011-06-03T10:00:00-07:00, 2011-06-03T10:00:00Z.
    // updatedMin   2011-06-03T10:00:00-07:00, 2011-06-03T10:00:00Z. ( updatedMin があると削除されたevent_idも返す)
    //
    public function list( Array $optParams ) {
        
        try {
            $calprop = $this->calprop;
            $google_calendar_id = $this->calprop->google_calendar_id;
            $return = $this->google_service->events->listEvents( $google_calendar_id, $optParams );
            
        } catch( Exception $e ) {
            throw new Exception( __METHOD__ );
            return false;
        } catch( LogicException $e ) {
            throw new Exception( __METHOD__ );
            return false;
        }        
        return op( $return )->items;
    }


    // Googleカレンダー同期用のデータ（ Google Eventインスタンス ）を生成
    //
    public static function get_google_event_data( Schedule $schedule, User $user = null ) {
        
        $schedule->load( 'user', 'user.dept', 'updator', 'updator.dept', 'attendees', 'attendees.dept' );
        
        $start = new Google_Service_Calendar_EventDateTime();
        $end   = new Google_Service_Calendar_EventDateTime();
        $start->setTimeZone( config( 'app.timezone' ));
        $end->setTimeZone( config( 'app.timezone' ));

        if( $schedule->all_day ) {
            $start->setDate( $schedule->start_date );

            // Googleカレンダーのend_date が１日後の日付にする
            //
            // $end->setDate(   $schedule->end_date   );

            $end_date = new Carbon( $schedule->end_date );
            $end_date->addDay();
            $end->setDate( $end_date->toDateString() );
            
            
        } else {
            $start->setDateTime( $schedule->start );
            $end->setDateTime( $schedule->end );
        }

        // Description
        //
        $separator    = self::getDescriptionSeparator();
        if( $schedule->id ) {
            $rn = "\n";
            $sp = "　";
            $description  = $schedule->memo;
            $route =  route( 'groupware.schedule.show', [ 'schedule' => $schedule->id ] );
        
            $description .= $separator.$rn;

            if( ! is_null( $user ) &&  ! $user->can( 'update', $schedule )) {
                $description .= 'この予定の編集権限がありません。' . $rn;
                $description .= 'Googleカレンダーで編集・削除してもFreeGroupに反映されません。';
                $description .= 'この予定を変更したいときは、作成者に依頼してください' .$rn .$rn;
            }
            $description .= $route. $rn .$rn;
            $description .= '作成者　：'. $schedule->user->dept->name. $sp. $schedule->user->name . $rn;
            if( $schedule->creator->id != $schedule->updator->id ) {
                $description .= '更新者　：'. $schedule->updator->dept->name. $sp. $schedule->updator->name . $rn;
            }
            $description .= '作成日時：'. $schedule->created_at->format( 'Y-m-d H:i' ) .$rn;
            if( $schedule->updated_at->gt( $schedule->created_at )) {
                $description .= '更新日時：'. $schedule->updated_at->format( 'Y-m-d H:i' ) . $rn;
            }
            foreach( $schedule->attendees as $attendee ) {
                $description .= '参加者　：'. $attendee->dept->name. $sp.  $attendee->name . $rn;
            }
        } else {
            $description = $schedule->memo;
        }
        
        $inputs = [   'start'       => $start, 
                      'end'         => $end,
                      'summary'     => $schedule->name,
                      'location'    => $schedule->place,
                      'description' => $description,
                    ];
        $event = new Google_Service_Calendar_Event( $inputs );
        return $event;
    }
    public static function getGoogleEventInstance( Schedule $schedule, User $user = null ) {
        return self::get_google_event_data( $schedule, $user );
    }
    
    
    public static function getDescriptionSeparator() {
        return config('groupware.schedule.google_description_separator');
        
    }
}

