<?php 

namespace App\myHttp\GroupWare\Controllers\SubClass;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Report;
use App\myHttp\GroupWare\Models\Task;


//　フォーム用の入力値成形用関数
//
class DateTimeInput {
    
    public $start_date;
    public $start_time;
    public $start;
    
    public $end_date;  // string
    public $end_time;  // string
    public $end;  // dateTime 

    public $due_date;
    public $due_time;
    
    public $all_day;
    
    public function __construct( $input = null ) {

        $this->all_day = ( op( $input )->all_day ) ? 1 : 0;
        
        if( $input instanceof Schedule or 
            $input instanceof Report      ) {
            // if_debug( __METHOD__, 'Schedule' );

            $this->start      = $this->start;
            $this->end        = $this->end;
            $this->start_date = $input->start_date;
            $this->end_date   = $input->end_date;

            if( $input->all_day ) {
                $this->start_time = null;
                $this->end_time   = null;
            } else {
                $this->start_time = $input->start->format( 'H:i' );
                $this->end_time   = $input->end->format( 'H:i' );
            }
        } elseif( $input instanceof Task ) {

            //
            //　タスク用
            //
            $this->due_date = $input->due_date->format( 'Y-m-d' );

            if( $input->all_day ) {
                $this->due_time = null;
            } else {
                $this->due_time = $input->due_time->format( 'H:i' );
            }

        } else {
            // if_debug( __METHOD__, 'null');

            $now = Carbon::now();
            
            $this->all_day = 0;
            // $this->start = new Carbon( $now->format( 'Y-m-d H:00'));
            // $this->end   = new Carbon( $now->addHour()->format( 'Y-m-d H:00' ));
            $this->start = new Carbon( $now->format( 'Y-m-d 10:00' ));
            $this->end   = new Carbon( $now->format( 'Y-m-d 11:00' ));
            
            $this->start_date = $this->start->format( 'Y-m-d' );
            $this->start_time = $this->start->format( 'H:i'   );
            $this->end_date   = $this->end->format( 'Y-m-d' );
            $this->end_time   = $this->end->format( 'H:i' );

            //　タスク用
            //
            $this->due_date = $this->start_date;
            $this->due_time = $this->start_time;

        }
        // if_debug( $this , $input);
    }

}