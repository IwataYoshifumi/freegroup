<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

// use App\Http\Controllers\Vacation\VacationSchedules;

use App\myHttp\GroupWare\ScheduledJobs\SyncFromGoogleCalendarToSchedule\FirstScheduledJob;
use App\myHttp\GroupWare\ScheduledJobs\SyncFromGoogleCalendarToSchedule\GoogleCalendarSyncJob;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Googleカレンダーの双方向同期処理
        //
        // $schedule->job( new CalPropsGsyncScheduledJob )->everyFiveMinutes();
        $schedule = GoogleCalendarSyncJob::scheduledSyncJob( $schedule );

        
        
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
