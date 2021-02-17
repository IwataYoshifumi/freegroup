<?php

namespace App\myHttp\GroupWare\ScheduledJobs\SyncFromGoogleCalendarToSchedule;

use Illuminate\Console\Scheduling\Schedule;

use App\myHttp\GroupWare\ScheduledJobs\SyncFromGoogleCalendarToSchedule\FirstScheduledJob;

class GoogleCalendarSyncJob {
    
    private const  SYNC_JOB_INTERVAL = 5;   // Googleカレンダー双方向同期ジョブ実行間隔（デフォルト５分）
                                            // 設定ファイル　config/groupware/google_calendar.php で実行間隔を設定する
    
    //　スケジュールジョブの実行間隔　kernel.php 内で利用
    //
    public static function scheduledSyncJob( Schedule $schedule ) {

        $interval = config( 'groupware.google_calendar.sync_job_interval' );
        $interval = ( is_integer( $interval )) ? $interval : self::SYNC_JOB_INTERVAL ;
        if( $interval <= 0  ) { $interval = self::SYNC_JOB_INTERVAL; }
        if( $interval >= 60 ) { $interval = 60; }

        if_debug( $interval );

        if( $interval == 1 ) {
            $schedule = $schedule->job( new FirstScheduledJob )->everyMinute();
        } elseif( $interval == 2 ) {
            $schedule = $schedule->job( new FirstScheduledJob )->everyTwoMinutes();
        } elseif( $interval == 3 ) {
            $schedule = $schedule->job( new FirstScheduledJob )->everyThreeMinutes();
        } elseif( $interval == 4 ) {
            $schedule = $schedule->job( new FirstScheduledJob )->everyFourMinutes();
        } elseif( $interval == 5 ) {
            $schedule = $schedule->job( new FirstScheduledJob )->everyFiveMinutes();
        } elseif( $interval >= 5 and $interval < 10 ) {
            $schedule = $schedule->job( new FirstScheduledJob )->everyTenMinutes();
        } elseif( $interval >= 10 and $interval < 15  ) {
            $schedule = $schedule->job( new FirstScheduledJob )->everyFifteenMinutes();
        } elseif( $interval >= 15 and $interval < 30  ) {
            $schedule = $schedule->job( new FirstScheduledJob )->everyThirtyMinutes();
        } else {
            $schedule = $schedule->job( new FirstScheduledJob )->hourly();
        }
        // if_debug( $schedule );
        
        return $schedule;
    }
    
}