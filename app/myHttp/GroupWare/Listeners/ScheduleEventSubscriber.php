<?php

namespace App\myHttp\GroupWare\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Exception;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Schedule;

use App\myHttp\GroupWare\Events\ScheduleCreatedEvent;
use App\myHttp\GroupWare\Events\ScheduleUpdatedEvent;
use App\myHttp\GroupWare\Events\ScheduleCalendarHasChangedEvent;
use App\myHttp\GroupWare\Events\ScheduleDeletedEvent;

use App\myHttp\GroupWare\Jobs\GsyncScheduleCreatedJob;
use App\myHttp\GroupWare\Jobs\GsyncScheduleUpdatedJob;
use App\myHttp\GroupWare\Jobs\GsyncScheduleUnSyncJob;

class ScheduleEventSubscriber // implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    //  public function __construct() {
    //  }

    public function subscribe( $events ) {
        $events->listen( ScheduleCreatedEvent::class,               'App\myHttp\GroupWare\Listeners\ScheduleEventSubscriber@created' );
        $events->listen( ScheduleUpdatedEvent::class,               'App\myHttp\GroupWare\Listeners\ScheduleEventSubscriber@updated' );
        $events->listen( ScheduleCalendarHasChangedEvent::class,    'App\myHttp\GroupWare\Listeners\ScheduleEventSubscriber@calendarHasChanged' );
        $events->listen( ScheduleDeletedEvent::class,               'App\myHttp\GroupWare\Listeners\ScheduleEventSubscriber@deleted' );
    }
    
    public function created( ScheduleCreatedEvent $event ) {
        
        $schedule = $event->schedule;
        $calendar = $schedule->calendar;
        
        if( is_debug() ) {
            $log  = " : schedule_id : "   . $schedule->id;
            $log .= " : schedule_name : " . $schedule->name;
            Log::debug( __METHOD__ . $log );
        }

        // キューを呼び出し
        Log::info( __METHOD__. " : This Job Do Nothing by now ");

    }
    
    public function updated( ScheduleUpdatedEvent $event ) {
        
        $schedule = $event->schedule;
        $calendar = $schedule->calendar;
        
        if( is_debug() ) {
            $log  = " : schedule_id : "   . $schedule->id;
            $log .= " : schedule_name : " . $schedule->name;
            $log .= " : calendar_id : "   . $calendar->id;
            Log::debug( __METHOD__ . $log );
        }

        // キューを呼び出し
        Log::info( __METHOD__. " : This Job Do Nothing by now ");
    }
    
    public function calendarHasChanged( ScheduleCalendarHasChangedEvent $event ) {

        $schedule = $event->schedule;
        $calendar = $schedule->calendar;

        if( is_debug() ) {
            $log  = " : schedule_id : "   . $schedule->id;
            $log .= " : schedule_name : " . $schedule->name;
            $log .= " : calendar_id : "   . $calendar->id;
            Log::debug( __METHOD__ . $log );
        }

        // キューを呼び出し
        Log::info( __METHOD__. " : This Job Do Nothing by now ");
    }
    
    public function deleted( ScheduleDeletedEvent $event ) {

        $schedule = $event->schedule;
        $calendar = $schedule->calendar;
        
        if( is_debug() ) {
            $log  = " : schedule_id : "   . $schedule->id;
            $log .= " : schedule_name : " . $schedule->name;
            $log .= " : calendar_id : "   . $calendar->id;
            Log::debug( __METHOD__ . $log );
        }
        // キューを呼び出し
        Log::info( __METHOD__. " : This Job Do Nothing by now ");
    }

}
