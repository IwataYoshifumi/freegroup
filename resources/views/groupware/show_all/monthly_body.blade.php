@php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use Carbon\Carbon;

use App\Models\Customer;

use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\TaskProp;

use App\myHttp\GroupWare\Controllers\Search\SearchSchedule;
use App\myHttp\GroupWare\Controllers\Search\SearchSchedulesAndTasks;
use App\myHttp\GroupWare\Controllers\Schedule2IndexController;



$d = 0;

$row = 1;
$col = 1;
#if_debug( $returns );
@endphp

@foreach( $returns['dates'] as $d => $date )
    @php
        if( $date->eq( $today )) {
            $box_class = "today_box";
        } elseif( $date->month != $base_date->month ) {
            $box_class = "other_month_box";
        } else {
            $box_class = "";
        }
    @endphp

    @if( $loop->first or $date->isSunday() ) <div class="row no-gutters"> @endif

        <div class="col border border-dark date_box shadow {{ $box_class }} row{{ $row }} col{{ $col }}">
            <div class="row no-gutters">
                <div class="col-12 date_item" data-date="{{ $date->format( 'Y-m-d' ) }}">
                    {{ $date->format( 'd' ) }}
                </div>
            </div>
        </div>
    @php
    $col++;
    @endphp

    @if( $date->isSaturday() ) 
        @php
        $row++;
        $col=1;
        @endphp
        </div> 
    @endif
@endforeach

