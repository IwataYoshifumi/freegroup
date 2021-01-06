<?php

namespace App\myHttp\GroupWare\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Schedule;

class ScheduleCalendarHasChangedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $schedule;
    
    public function __construct( Schedule $schedule, Calendar $old_calendar ) {

        $this->schedule = $schedule;
        $this->new_calendar = $schedule->calendar;
        $this->old_calendar = $old_calendar;
        $this->creator  = $schedule->user;
        
        if( is_debug() ) {
            $log  = " : schedule_id : ". $this->schedule->id;
            $log .= " : calendar_id : new : ". $this->new_calendar->id;
            $log .= " : calendar_id : old : ". $this->old_calendar->id;
            Log::debug( __METHOD__ . $log );
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn() {
        return new PrivateChannel('channel-name');
    }
    
}
