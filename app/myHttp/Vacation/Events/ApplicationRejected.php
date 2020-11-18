<?php

namespace App\Http\Controllers\Vacation\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Vacation\Application;

class ApplicationRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $application;
    
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct( $application )
    {
        //
        // dd( $application );
        $this->application = $application;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
    
    public function getApplicant() {
        
        // dd( $this->application->applicant );
        
        return $this->application->applicant;
    }
    
    public function getApplication() {
        return $this->application;
    }
    
}
