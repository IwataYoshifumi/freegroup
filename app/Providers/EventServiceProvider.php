<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        //　有給休暇申請システム用イベント
        //
        'App\Http\Controllers\Vacation\Events\ApplicationApproved' => [ 'App\Http\Controllers\Vacation\Listeners\ApplicationApprovedListener' ],
        'App\Http\Controllers\Vacation\Events\ApplicationRejected' => [ 'App\Http\Controllers\Vacation\Listeners\ApplicationRejectedListener' ],

        //  グループウェア関連
        //
        //  関連スケジュールのGoogle同期
        //
        'App\myHttp\GroupWare\Events\SyncRelatedScheduleToGoogleCalendarEvent' => [ 'App\myHttp\GroupWare\Listeners\SyncRelatedScheduleToGoogleCalendarListener' ],
        

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
