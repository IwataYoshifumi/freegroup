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

class InitUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( User $user ) {
        //
        $this->user = $user;
        if( is_debug() ) { Log::debug( __CLASS__ . " : user_id : $user->id" ); }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() : void {

        InitUser::whenUserHasCreatedFirst( $this->user );
        return;
        
    }
}
