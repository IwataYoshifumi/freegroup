<?php

namespace App\Http\Controllers\Vacation;

use Illuminate\Support\Facades\Route;
use Illuminate\Console\Scheduling\Schedule;
// use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use Carbon\Carbon;
use DB;

use App\Http\Controllers\Controller;
use App\Models\Vacation\User;
use App\Models\Vacation\Dept;
use App\Models\Vacation\Application;
use App\Models\Vacation\Approval;
use App\Models\Vacation\Vacation;
use App\Models\Vacation\VacationList;
// use App\Models\Vacation\Paidleave;
// use App\Http\Requests\Vacation\VacationRequest;

class VacationSchedules extends Controller
// class VacationSchedules
{
    //  Console/kernel に設定するスケジュール
    //
    public static function schedule( Schedule $schedule ) {

        //　有効期限切れ有給休暇の処理
        //
        $schedule->call( function() {
                VacationSchedules::expire_vacations();
        })
        // ->emailOutputTo( 'iwata@network-tokai.jp')
        //->everyMinute()
        ->dailyAt('0:01')
        ->timezone('Asia/Tokyo');

        // 　有効期限切れ後の申請キャンセル分の処理
        //
        $schedule->call( function() {
            VacationSchedules::check_expired_vacations();
        })
        // ->emailOutputTo( 'iwata@network-tokai.jp')
        // ->everyMinute()
        ->dailyAt('0:30')
        ->timezone('Asia/Tokyo');

        //　催促メール
        //
        $schedule->call( function() {
                Application::DoMentionIncompleted();
        })
        //->everyMinute()
        ->dailyAt( '7:50' )
        ->weekdays()
        ->timezone('Asia/Tokyo');
        
    }

    
    //　有給休暇の有効期限切れ処理
    //
    public static function expire_vacations() {

        echo( "VacationSchedules::expire_vacations() excutes\n");

        $yesterday = Carbon::yesterday()->toDateString();
        $vacations = Vacation::where( 'action', '割当' )
                             ->where( 'expire_date', '<=', $yesterday )
                             ->where( 'done_expired', false )
                             ->get();
        
        DB::transaction( function() use( $vacations ) {
            foreach( $vacations as $vacation ) {
                echo( "expire vacations $vacation->id\n" );

                $vacation->expire_paidleave();
            }
        });
        return true;
    }
    
    //　有効期限切れデータのチェック
    //
    public static function check_expired_vacations() {
        
        echo( "VacationSchedules::check_expired_vacations() excuting\n");
        
        $yesterday = Carbon::yesterday()->toDateString();
        $vacations = Vacation::where( 'action', '割当' )
                            //  ->where( 'expire_date', '<=', $yesterday )
                             ->where( 'done_expired', true )
                             ->where( 'remains_num', '>', 0 )
                             ->get();
        // dd( $vacations);
        
        DB::transaction( function() use( $vacations ) {
            foreach( $vacations as $vacation ) {
                echo( "check_expired_vacations : expired Vacation ID : $vacation->id\n" );

                $vacation->check_expired_paidleave();
            }
        });
        return true;

    }

    
    
}
