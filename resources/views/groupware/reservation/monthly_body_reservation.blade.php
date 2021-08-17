@php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use Carbon\Carbon;

use App\Models\Customer;

use App\myHttp\GroupWare\Models\Facility;
use App\myHttp\GroupWare\Models\Reservation;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\User;

use App\myHttp\GroupWare\Controllers\Search\SearchReservation;
use App\myHttp\GroupWare\Controllers\Search\SearchReservationsAndTasks;
use App\myHttp\GroupWare\Controllers\Reservation2IndexController;

$d = 0;

$row = 1;
$col = 1;

$array_multi  = [];

foreach( $returns as $d => $return ) {
    $array_multi[$d] = [];
}

$array_single = $array_multi;
$array_time   = $array_multi;

#if_debug( $array_multi, $array_single, $array_time, $returns );

$rows = $returns['rows'];
$cols = $returns['cols'];
$orders = [];
foreach( $returns['dates'] as $d => $date ) {
    $orders[$d] = 0;
}

$start_date_of_calendar = Arr::first( $returns['dates'] );
$end_date_of_calendar   = Arr::last(  $returns['dates'] );

$others = [];  // 表示しきれずその他何件と表示するための配列（キー　日付、値　その他の件数）

@endphp

{{-- 複数日の予定を表示 --}}
{{--
@foreach( $returns['multi'] as $i => $reservation )
    @php
    
    $start = new Carbon( $reservation->start->format( 'Y-m-d 00:00:00' ));
    $end   = new Carbon( $reservation->end->format(   'Y-m-d 23:59:59' ));
    
    if( $end->gt( $end_date_of_calendar )) { $end = $end_date_of_calendar->copy(); }
    
    $start_row = $rows[ $start->format( 'Y-m-d' ) ];
    $start_col = $cols[ $start->format( 'Y-m-d' ) ];
    $end_row   = $rows[ $end->format( 'Y-m-d' ) ];
    $end_col   = $cols[ $end->format( 'Y-m-d' ) ];
    
    $style = $reservation->style();
    
    #$span = $end->diffInDays( $start ) + 1;
    #$span = $reservation->getNumDates();
    #if_debug( $reservation->purpose, $span );

    $d = $start->copy();

    @endphp

    @while( $d->lte( $end ))

        @php
        $span = $d->diffInDays( $start ) + 1;
        $start_row = $rows[ $start->format( 'Y-m-d' ) ];
        $start_col = $cols[ $start->format( 'Y-m-d' ) ];
        $end_row   = $rows[ $d->format( 'Y-m-d' ) ];
        $end_col   = $cols[ $d->format( 'Y-m-d' ) ];
        $d_date    = $d->format( 'Y-m-d' );

        if( $d->diffInDays( $start ) == 0 or $d->isSunday() ) {
            $order = $orders[$d_date] + 1;
        }
        
        if( $order >= 6 ) {
            $others[$d_date] = op( $others )[$d_date] + 1;
        }
        $orders[$d_date] = $order;
        
        $reservation_class = "schedule calender_item facility_" . $reservation->facility_id;
        // $data_reservation = " data-reservation_id='$reservation->id' data-calendar_id='$reservation->calendar_id' ";
        $data = "data-object='reservation' data-object_id=" . $reservation->id;
        
        $style = $reservation->style() . "position: absolute; z-index: 100;";
        @endphp
    
        @if( $order >= 6 and ( $d->isSaturday() or $d->diffInDays( $end ) == 0 )) 
            {{-- その他〇〇件 --}

        @elseif( $d->isSaturday() ) 
        
            <div class="row{{ $start_row }} col{{ $start_col }} span{{ $span }} cal_cell">
                <div class="order{{ $order }} {{ $reservation_class }} multi_schedule object_to_show_detail" style="{{ $style }}" {!! $data !!}> {{-- htmlspecialchars OK --}
                    @if( $user_id != $reservation->user_id ) 【{{ $reservation->user->name }}】 @endif
                
                    {{ $reservation->purpose }}
                    @if( ! $reservation->all_day )
                        {{ $reservation->p_time('monthly') }}
                    @endif
                </div>
            </div>
            @php
                $start = $d->copy()->addDay();
            @endphp
        @elseif( $d->diffInDays( $end ) == 0 )

            <div class="row{{ $start_row }} col{{ $start_col }} span{{ $span }} cal_cell">
                <div class="order{{ $order }} {{ $reservation_class }} multi_schedule object_to_show_detail" style="{{ $style }}" {!! $data !!}> {{-- htmlspecialchars OK --}
                    @if( $user_id != $reservation->user_id ) 【{{ $reservation->user->name }}】 @endif
                    {{ $reservation->purpose }} {{ $reservation->p_time( 'monthly' ) }}
                </div>
            </div>
        @endif

        @php
        $d->addDay();
        @endphp
    @endwhile
@endforeach
--}}
{{-- 終日予定、タスク、１日以内の予定　を表示 --}}

@foreach( [ $returns['single'], $returns['time'] ] as $return_values )
    @foreach( $return_values as $Item )

        @if( $Item instanceof Reservation )    
            @php
            $reservation = $Item;
            
            $start = $reservation->start->copy();
            $d_date = $start->format( 'Y-m-d' );
            
            $start_row = $rows[ $d_date ];
            $start_col = $cols[ $d_date ];
            
            $style = $reservation->style();
        
            $reservation_class = "schedule calendar_item facility_" . $reservation->facility_id;
            $data = "data-object='reservation' data-object_id=" . $reservation->id;
            
            $num_dates = $reservation->getNumDates();
            
            @endphp
            
            @if( $num_dates >= 2 ) 
                @php
                $start = $reservation->start->copy();
                $end   = $reservation->end->copy();
                $d     = $start->copy();                
                @endphp
                @for( $j = 1; $j <= $num_dates; $j++ )
                    @php
                    $d_date = $d->format( 'Y-m-d' );
                    $order = $orders[$d_date] + 1;
                    $orders[$d_date] = $order;

                    $start_row = $rows[ $d_date ];
                    $start_col = $cols[ $d_date ];

                    @endphp
    
                    <div class="row{{ $start_row }} col{{ $start_col }} span1 cal3" style="">
                        <div class="order{{ $order }} {{ $reservation_class }} single_schedule object_to_show_detail d-flex" style="{{ $style }}" {!! $data !!}> {{-- htmlspecialchars OK --}}
                            @if( $order < 6 )
                                @php
                                #dump( $loop );
                                @endphp
                                
                                <div class="flex-fill">
                                @if( $reservation->all_day )
                                    終日                     
                                @else
                                    @if( $j == 1 )
                                        {{ $reservation->start_time() }}～
                                    @elseif( $j == $num_dates ) 
                                        ～{{ $reservation->end_time() }}
                                    @else 
                                        終日
                                    @endif
                                @endif
                                </div>
                                <span class="text-right text-truncate flex-fill pr-2">
                                {{ $reservation->user->name }} 
                                </span>
                            @else
                                {{-- その他〇〇件 --}}
                                @php
                                $others[$d_date] = op( $others )[$d_date] + 1;
                                @endphp
                            @endif
                        </div>
                    </div>
                    @php
                    $d->addDay();
                    @endphp
                @endfor
            @else
                @php
                $order = $orders[$d_date] + 1;
                $orders[$d_date] = $order;
                
                
                @endphp
                <div class="row{{ $start_row }} col{{ $start_col }} span1 cal3" style="">
                    <div class="order{{ $order }} {{ $reservation_class }} single_schedule object_to_show_detail d-flex" style="{{ $style }}" {!! $data !!}> {{-- htmlspecialchars OK --}}
                        @if( $order < 6 )
                            <span class="text-left  flex-fill"                   >{{ $reservation->p_time( 'monthly' ) }}</span>
                            <span class="text-right flex-fill text-truncate pr-2">{{ $reservation->user->name          }} </span>
                        @else
                            {{-- その他〇〇件 --}}
                            @php
                            $others[$d_date] = op( $others )[$d_date] + 1;
                            @endphp
                        @endif
                    </div>
                </div>
            @endif
            
        @endif
    @endforeach
@endforeach

{{-- 表示しきれなかったその他の予定・タスクの件数を表示 --}}

@foreach( $others as $d_date => $num )
    @php
    $start_row = $rows[ $d_date ];
    $start_col = $cols[ $d_date ];      
    
    @endphp
    <div class="row{{ $start_row }} col{{ $start_col }} span1 cal3">
        <div class="date_item calendar_item order6 single_reservation other_item" data-date="{{ $d_date }}">
            その他 {{ $num }} 件・・・
        </div>
    </div>    
@endforeach

</div>

<script>
    //　その他をクリックすると週表示へ移動
    //
    $(".other_item").click( function() {
        var date = $(this).data('date');

        var url  = "{{ route( 'groupware.reservation.weekly' ) }}";
        $('#base_date').val( date );
        $('#search_form').attr( 'action', url );
        $('#search_form').submit();
    });
</script>