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
use Exception;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Schedule;

class ScheduleUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $schedule;

    public function __construct( $schedule ) {

        $this->schedule = ( $schedule instanceof Schedule ) ? $schedule : Schedule::find( $schedule->id );
        $this->calendar = $schedule->calendar;

        if( is_debug() ) {
            $log  = " : schedule_id : "   . $this->schedule->id;
            $log .= " : schedule_name : " . $this->schedule->name;
            $log .= " : calendar_id : "   . $this->calendar->id;
            $log .= " : updator : "       . $this->schedule->updator->id;
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
