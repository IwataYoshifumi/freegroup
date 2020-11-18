<?php

namespace App\Http\Controllers\Vacation\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Http\Controllers\Vacation\Events\ApplicationRejected;
use App\Http\Controllers\Vacation\Notifications\NoticeApplicationRejected;

class ApplicationRejectedListener
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

    /**
     * Handle the event.
     *
     * @param  ApplicationApproved  $event
     * @return void
     */
    public function handle(ApplicationRejected $event)
    {
        //
        // dd( $event );
        $applicant  =  $event->getApplicant();
        $application = $event->getApplication();
        $applicant->notify( new NoticeApplicationRejected( $application ) );
        
    }
}
