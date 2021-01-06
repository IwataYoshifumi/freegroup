<?php

namespace App\myHttp\GroupWare\ScheduledJobs\SyncFromGoogleCalendarToSchedule;

use DB;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Arr;

use App\Http\Helpers\MyGoogleCalendarClient;

class MyGoogleEventClass {
    
    public $id;
    public $etag;
    public $status;

    public $start;
    public $start_date;
    public $start_time;
    public $end;
    public $end_date;
    public $end_time;
    public $all_day;
    public $summary;
    public $description;
    public $location;
    public $updated;

    public function __construct( $google_event ) {

        $this->id          = $google_event->id;
        $this->etag        = $google_event->etag;
        $this->status      = $google_event->status;
        
        $separator = MyGoogleCalendarClient::getDescriptionSeparator();
        
        if( $google_event->status == 'confirmed' ) {

            $this->summary     = $google_event->summary;
            $this->location    = $google_event->location;

            $this->updated     = new Carbon( $google_event->updated );
            $this->updated->timezone( 'Asia/Tokyo' );
            $timezone = config( 'app.timezone' );
            $this->start       = $google_event->start;
            $this->start_date  = $google_event->start->getDate();
            $this->start_time  = $google_event->start->getDateTime();
            $this->end         = $google_event->end;
            $this->end_date    = $google_event->end->getDate();
            $this->end_time    = $google_event->end->getDateTime();
          
            $this->all_day     = ( $this->start_date ) ? 1 : 0;
             if( $this->all_day ) {
                $this->start_time  = new Carbon( $this->start_date." ". $timezone );

                // Googleカレンダーで終日とすると、end_date が１日後の日付になる対策
                //
                $this->end_date    = new Carbon( $this->end_date );
                $this->end_date    = $this->end_date->subDay();
                if( $this->start_time->gt( $this->end_date )) {
                    $this->end_date = $this->start_time;
                }
                
                $this->end_time    = new Carbon( $this->end_date." ". $timezone );
            } else {

                $this->start_date  = new Carbon( $this->start_time );
                $this->start_date  = $this->start_date->format( 'Y-m-d' );
                $this->end_date    = new Carbon( $this->end_time   );
                $this->end_date    = $this->end_date->format( 'Y-m-d' );
            }

            // Description の整形
            //
            $escaped_separator = preg_quote( $separator );
            $pattern = "/((.|\R)*)($escaped_separator){1}/";
            if( preg_match( $pattern, $google_event->description, $matches )) {
                $description = op( $matches )[1];    
            } else {
                $description = $google_event->description;
            }
            $description = preg_replace( '/&amp;/',   "&", $description );
            $description = preg_replace( '/&gt;/',   ">",  $description );
            $description = preg_replace( '/&lt;/',   "<",  $description );
            $description = preg_replace( '/&nbsp;/', " ",  $description );
            $description = preg_replace( '/&quot;/', "\"", $description );
            $description = preg_replace( '/<br>/',   "\n", $description );
            
            $this->description = $description;
        }
    }
    
    
}