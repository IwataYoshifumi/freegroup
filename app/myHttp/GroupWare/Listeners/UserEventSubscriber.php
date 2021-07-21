<?php

namespace App\myHttp\GroupWare\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Group;

use App\myHttp\GroupWare\Events\UserCreateEvent;
use App\myHttp\GroupWare\Events\UserTransferDeptEvent;
use App\myHttp\GroupWare\Events\UserRetireEvent;
use App\myHttp\GroupWare\Events\UserReturnEvent;

// use App\myHttp\GroupWare\Models\Initialization\initUser;

class UserEventSubscriber // implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    //  public function __construct() {
    //  }


    public function subscribe( $events ) {
        $events->listen( UserCreateEvent::class,        'App\myHttp\GroupWare\Listeners\UserEventSubscriber@created' );
        $events->listen( UserTransferDeptEvent::class,  'App\myHttp\GroupWare\Listeners\UserEventSubscriber@transfer_department' );
        $events->listen( UserRetireEvent::class,        'App\myHttp\GroupWare\Listeners\UserEventSubscriber@retired' );
        $events->listen( UserReturnEvent::class,        'App\myHttp\GroupWare\Listeners\UserEventSubscriber@return' );
    }
    
    public function created( UserCreateEvent $event ) {
        $user = $event->user;
        // if_debug( 'created', $user );
        
    }
    
    public function transfer_department( UserTransferDeptEvent $event ) {
        // if_debug( 'transfer_department', $this, $event);
        
    }
    
    public function retired() {
        // _d( 'retired', $this);
        return true;
    }
    
    public function return() {
        // _d( 'return', $this );
        return true;
    }
    
    

}
