<?php

namespace App\myHttp\GroupWare\Jobs\File;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;


use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\AccessList;
use App\myHttp\GroupWare\Models\File as MyFile;
use App\myHttp\GroupWare\Models\Fileable;;


use App\myHttp\GroupWare\Jobs\GoogleCalendar\GCalSyncDelete;

class DeleteFilesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $files;  // MyFile クラスのコレクション
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $files ) {
        //
        
        $this->files = $files;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() : void {
        //
        $files = $this->files;
        
        if( count( $files ) == 0 ) { return; }
        
        $file_ids  = $files->pluck( 'id' )->toArray();
        Fileable::whereIn( 'file_id', $file_ids )->delete();
        
        try {
            Storage::delete( $files->pluck( 'path' )->toArray() );
        } catch( Exception $e ) {
            throw new Exception( __METHOD__ . ' : Delete Files Error ');
        }
        $files->toQuery()->delete();
        
        return;
        
    }
}
