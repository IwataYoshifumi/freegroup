<?php

namespace App\myHttp\GroupWare\Jobs\Init;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use DB;
use Exception;
use Carbon\Carbon;

use App\myHttp\GroupWare\Models\User;

use App\myHttp\GroupWare\Models\Initialization\InitUser;
use App\myHttp\GroupWare\Jobs\Init\InitUseJob;

class InitAllUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {
        //
        if( is_debug() ) {
            Log::debug( __CLASS__ . " : The Job has been dispatched." );
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() : void {
        //
        $users = User::all();

        foreach( $users as $user ) {
            // InitUser::whenUserHasCreatedFirst( $user );
            InitUserJob::dispatch( $user );
        }
        
        return;
        
    }
}
