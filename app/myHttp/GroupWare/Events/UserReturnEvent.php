<?php

namespace App\myHttp\GroupWare\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\myHttp\GroupWare\Models\User;

class UserReturnEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    
    public function __construct( User $user ) {

        $this->user = $user;
        // dump( $this );
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
