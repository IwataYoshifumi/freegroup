<?php

namespace App\Http\Controllers\Vacation\Listeners;

use App\Http\Controllers\Vacation\Events\ApplicationApproved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
    }
}
