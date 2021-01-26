@php

use Carbon\Carbon;

$today = new Carbon( 'today' );

Carbon::setWeekStartsAt(Carbon::SUNDAY); // 週の最初を日曜日に設定
Carbon::setWeekEndsAt(Carbon::SATURDAY); // 週の最後を土曜日に設定

$button['今年']['start'] = Carbon::parse( 'first day of January' )->format('Y-m-d');
$button['今年']['end']   = Carbon::parse( 'last day of December'  )->format('Y-m-d'); 

$button['先月']['start'] = Carbon::parse( 'first day of last month' )->format('Y-m-d');
$button['先月']['end']   = Carbon::parse( 'last day of last month'  )->format('Y-m-d');

$button['今月']['start'] = Carbon::parse( 'first day of this month' )->format('Y-m-d');
$button['今月']['end']   = Carbon::parse( 'last day of this month' )->format('Y-m-d');                                    

$button['先週']['start'] = $today->copy()->startOfWeek()->subDays(7)->format('Y-m-d');
$button['先週']['end']   = $today->copy()->endOfWeek()->subDays(7)->format('Y-m-d');                         

$button['今週']['start'] = $today->copy()->startOfWeek()->format('Y-m-d');
$button['今週']['end']   = $today->copy()->endOfWeek()->format('Y-m-d');

$button['今日']['start'] = $today->format('Y-m-d');
$button['今日']['end']   = $today->format('Y-m-d'); 


@endphp


<div class="w-100 container">
    <div>
        {{ Form::text( $start_name, $start_value, [ 'class' => 'datepicker', 'id' => 'start_date' ] ) }}
        <span>～</span>
        {{ Form::text( $end_name,   $end_value  , [ 'class' => 'datepicker',   'id' => 'end_date' ] ) }}
    </div>
    <div>
        @foreach( $button as $key => $date ) 
            <a class="col-3 col-lg-1 m-1 btn btn-sm btn-outline btn-outline-dark date_button" data-start='{{ $date['start'] }}' data-end='{{ $date['end'] }}'>{{ $key }}</a>
        @endforeach
    </div>
    <script>
        $('.datepicker').datepicker( { numberOfMonths : 2, showButtonPanel : true } );

        $('.date_button').click( function(){
            $('#start_date').val( $(this).data('start') ); 
            $('#end_date').val( $(this).data('end') ); 
        });
    </script>  
</div>