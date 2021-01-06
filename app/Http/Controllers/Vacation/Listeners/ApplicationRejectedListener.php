<?php

namespace App\Http\Controllers\Vacation\Listeners;

use App\Http\Controllers\Vacation\Events\ApplicationRejected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
     * @param  ApplicationRejected  $event
     * @return void
     */
    public function handle(ApplicationRejected $event)
    {
        //
    }
}
