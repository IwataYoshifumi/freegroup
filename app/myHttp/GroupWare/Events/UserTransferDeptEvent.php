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
use App\myHttp\GroupWare\Models\Dept;

class UserTransferDeptEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    
    public function __construct( $user, $old_dept ) {

        $this->user     = $user;
        $this->old_dept = $old_dept;
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
