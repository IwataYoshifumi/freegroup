<?php

namespace App\myHttp\GroupWare\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Tasks;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Exception;
use Spatie\GoogleCalendar\Event;

use App\myHttp\GroupWare\Events\SyncRelatedScheduleToGoogleCalendarEvent;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\ScheduleType;

class SyncRelatedScheduleToGoogleCalendarListener // implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //　関連社員で関連予定にGoogle 同期設定している場合は、Google カレンダー同期を行う。
    //
    public function handle( SyncRelatedScheduleToGoogleCalendarEvent $event ) {
        //
        // dump( $event );
        
        $schedule = $event->schedule;
        $user = $schedule->user;
        $users = $schedule->users;

        // dump( $users->count() );
        
        foreach( $users as $u ) {
            // dump( "$u->id, $u->name" );
            if( $u->id != $user->id ) { // 関係者と作成者が同じ場合はGoogle同期しない

                $type = ScheduleType::where( 'user_id', $u->id)->where( 'class', 'relation' )
                                    ->whereNotNull( 'google_calendar_id' )
                                    ->whereNotNull( 'google_id')
                                    ->has( 'files' )
                                    // ->whereNotNull( 'google_private_key_file')
                                    ->first();
                                    
                // dump( $type );
                if( ! is_null( $type )) {
                    dump( "OK, $u->id, $u->name, $type->relation, $type->color, $type->text_color"  );
                    $this->sync_google( $type, $schedule );
                    
                    
                } else {
                    dump( "null $u->name" );
                }
            }
        }
    }
    
    public static function sync_google( ScheduleType $type, Schedule $schedule ) {

        $google_calendar_id = $type->google_calendar_id;
        $google_private_key = $type->google_private_key_file();

        try {
            // Google カレンダーと同期
            //
            $client = new Google_Client();
            $client->setApplicationName('Network Tokai Groupware');
            $client->setScopes(Google_Service_Calendar::CALENDAR);
            $client->setAuthConfig( storage_path( 'app/'. $google_private_key->path ));
            $service = new Google_Service_Calendar( $client );
            
            $start = new Google_Service_Calendar_EventDateTime();
            $start->setTimeZone( 'Asia/Tokyo');
            $start->setDateTime( $schedule->start_time );
            $end    = new Google_Service_Calendar_EventDateTime();
            $end->setTimeZone( 'Asia/Tokyo');
            $end->setDateTime( $schedule->end_time );
            
            $inputs = [ 'start' => $start, 
                        'end'   => $end,
                        'summary'   => $schedule->name,
                        'location'  => $schedule->place,
                        'description' => $schedule->memo,
                    ];
                    
            
            $event = new Google_Service_Calendar_Event( $inputs );
            // $google_calendar_event_id = $schedule->users( )
            
            $event = $service->events->insert( $google_calendar_id, $event );
            // dd( $type->user );
    
    
            $schedule->users()->updateExistingPivot( $type->user->id , [ 'google_calendar_event_id' =>  $event->id ] );
        
            
        } catch( Google_Service_Exception $e ) {
            // dd( 'error', $e );
            session::flash( 'info_message', 'Googleカレンダーの同期ができませんでした。Googleカレンダーとスケジュール種別　双方の設定を確認してください。');
            return null;
        }
        
    }
}
