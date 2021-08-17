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

@if( empty( $returns['users'] ))

    <div class="alert-danger h3 m-3 p-3" style="z-index: 1000; position: absolute; top: 30px; left: 30%;" id="alert-info">この条件では何も検索されませんでした。</div>

    
    @foreach( $returns['dates'] as $d => $date )
        <div class="row no-gutters"> 
        @php
            if( $date->eq( $today )) {
                $box_class = "today_box";
            } elseif( $date->month != $base_date->month ) {
                $box_class = "other_month_box";
            } else {
                $box_class = "";
            }
        @endphp
        @if( $loop->first ) 
            <div class="col span01 border border-dark date_box shadow {{ $box_class }} row{{ $row }} col01 ">
                <div class="row no-gutters">
                    <div class="col-12 date_item">
                    </div>
                </div>
            </div>
        @endif
            <div class="col span01 border border-dark date_box shadow {{ $box_class }} row{{ $row }} col0{{ $col+1 }}">
                <div class="row no-gutters">
                    <div class="col-12 date_item" data-date="{{ $date->format( 'Y-m-d' ) }}">
                        &nbsp;{{ $date->format( 'n/d' ) }}
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
@endif

@push( 'script_to_move_daily_page' )
<script>
    $('.date_item').on( 'click', function() {
        var date = $(this).data( 'date' );
        var url  = "{{ url( '/groupware/show_all/daily/' ) }}";
        // var url  = "{{ url( '/groupware/show_all/dialog/daily/' ) }}";
        $('#base_date').val( date );
        $('#search_form').attr( 'action', url );
        $('#search_form').submit();
        
    });
</script>
@endpush
