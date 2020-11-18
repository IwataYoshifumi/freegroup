<?php

namespace App\Http\Controllers\Vacation\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Http\Controllers\Vacation\Events\ApplicationApproved;
use App\Http\Controllers\Vacation\Notifications\NoticeApplicationApproved;

class ApplicationApprovedListener
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
    public function handle(ApplicationApproved $event)
    {
        //
        // dd( $event );
        $applicant  =  $event->getApplicant();
        $application = $event->getApplication();
        $applicant->notify( new NoticeApplicationApproved( $application ) );
        
    }
}
