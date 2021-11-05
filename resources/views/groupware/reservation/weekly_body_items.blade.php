@php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use Carbon\Carbon;

use App\Models\Customer;

use App\myHttp\GroupWare\Models\Reservation;
use App\myHttp\GroupWare\Models\Facility;
use App\myHttp\GroupWare\Models\User;

$d = 0;

$row = 1;
$col = 1;

$array_multi  = [];

foreach( $returns as $d => $return ) {
    $array_multi[$d] = [];
}

$array_single = $array_multi;
$array_time   = $array_multi;
$array_task   = $array_multi;

#if_debug( $array_multi, $array_single, $array_time, $array_task, $returns );

$rows = $returns['rows'];
$cols = $returns['cols'];
$orders = [];
foreach( $returns['dates'] as $d => $date ) {
    $orders[$d] = 1;
}

$start_date_of_calendar = Arr::first( $returns['dates'] );
$end_date_of_calendar   = Arr::last(  $returns['dates'] );

$others = [];  // 表示しきれずその他何件と表示するための配列（キー　日付、値　その他の件数）

$dates      = $returns['dates'];
$facilities = $returns['facilities'];


@endphp

<div >
    @php
    $i = 1;
    $top = 0;
    $next_top = 0;

    $template_orders = [];
    foreach( $dates as $date ) {
        $tmplate_orders[$date->format('Y-m-d')] = 0;
    }
    
    @endphp

    @foreach( $facilities as $facility )
        @php
        
        $orders = $tmplate_orders;

        $max = $returns[$facility->id]['max_num_of_objects'] + 1;
        // $max = 10;
        $height = 28 + $max * 20;
        $top = $next_top;

        $next_top = $top + $height;
        $row_style = "top: " . $top . "px; height: " . $height . "px;"; // ユーザごとの行の高さ、トップの位置
        $top_style = "top: " . $top . "px";
        
        @endphp
        
        <div class="col01 span01 cal3 border border-dark bg-white" style="{{ $row_style }}">
            <div class="order1 font-weight-bold text-truncate">
                {{ $facility->category }} {{ $facility->sub_category }}<br>
                {{ $facility->name }}<br>
                @if( 0 && is_debug() ) 
                    Facility ID : {{ $facility->id }} : {{ $max }}/{{ $row_style }} 
                @endif
            </div>
        </div>
        
        @foreach( $dates as $date )
            @php
            $date_text = $date->format('Y-m-d');
            $col = $returns['cols'][$date_text] + 1;
            
            $items = $returns[$facility->id][$date_text];
            #if_debug( $items );
            
            if( $date->eq( $today )) {
                $box_class = "today_box";
            } elseif( $date->month != $base_date->month ) {
                $box_class = "other_month_box";
            } else {
                $box_class = "bg-white";
            }
        
            @endphp
        
            <div class="col0{{ $col }} span01 cal3 border border-dark {{ $box_class }}" style="{{ $row_style }}">
                <div class="order0 date_item click_to_open_modal_create_reservation" data-date="{{ $date_text }}">
                    &nbsp; {{ $date->format( 'n/j' ) }}
                </div>
            </div>
            

            {{--            
            <div class="col0{{ $col }} span01 cal3 border border-dark" style="{{ $row_style }} z-index:100; pointer-events: none;"></div>
            --}}
            @foreach( $items['reservations'] as $reservation )
                @if( $reservation->getNumDates() >= 2 )
                    {{--
                      --
                      --  複数日の予定
                      --
                    --}}
                    @php
                    # dd( $reservation, $reservation->facility->id, $reservation->facility_id );
                    # $tmp_facility = $reservation->facility;
                    #$tmp_d     = new Carbon( $date->format( 'Y-m-d 23:59:59' ));
                    $tmp_d     = $date->copy();
                    $tmp_start = $date->copy();
                    if( $reservation->start->lt( $start_date_of_calendar )) {
                        $start_date = $start_date_of_calendar->copy();
                    } else {
                        $start_date = $reservation->start->copy();
                    }
                    $order = $orders[$tmp_d->format('Y-m-d')]+1;
                    $orders[$tmp_d->format('Y-m-d')] = $order;
    
                    #if( 0 and $reservation->id == 47 ) { dump( $orders ); }
    
                    for( $j = 1; $j <= 7; $j++ ) {
                        if( $tmp_d->gte( $reservation->end ) or ( $j != 1 and $tmp_d->isSunday() )) {
                            break;
                        }
                        $orders[$tmp_d->format('Y-m-d')] = $order;
                        $tmp_d->addDay();                                
                    }
                    
                    $span = ( $tmp_start->diffInDays( $tmp_d ) == 0 ) ? 1 : $tmp_start->diffInDays($tmp_d )  ;
                    #$span = $tmp_start->diffInDays( $tmp_d ) + 1 ;
                    $schedule_class = "schedule schedule_item";
                    $data = "data-object='reservation' data-object_id=" . $reservation->id;
                    $style = $reservation->style();

                    #if( 0 and $reservation->id == 47 ) { if_debug( $orders, $order, $span, $date->format( 'Y-m-d' ), $tmp_d->format( 'Y-m-d' ), $reservation->end->format( 'Y-m-d') );  }

                    @endphp
    
                    <div class="cal3 w-100" style="{{ $top_style }}">
                        <div class="col0{{ $col }} span0{{ $span }} cal3 calendar_item order{{ $order }} {{ $schedule_class }} object_to_show_detail" style="{{ $style }}" {!! $data !!}> {{-- htmlspecialchars OK --}}
                            <div class="d-flex">
                                <span class="flex-fill text-left text-truncate">
                                    @if( $reservation->all_day )
                                        終日
                                    @else
                                        @if( $reservation->start->gte( $start_date_of_calendar ) )
                                            {{ $reservation->start->format( 'H:i' ) }}
                                        @endif

                                    @endif
                                    @if( $reservation->end->gt( $end_date_of_calendar->copy()->addDay() )) 
                                        ～{{ $reservation->end->format( 'n月j日' ) }}
                                    @elseif( ! $reservation->all_day )
                                        ～{{ $reservation->end->format( 'n月j日 H:i' ) }}
                                    @endif
                                </span>
                                <span class="flex-fill text-right text-trunctate pr-2">{{ $reservation->user->name }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    {{--
                      --
                      -- １日の予約を表示
                      --
                      --}}
                    @php
                    $complete = "";
                    $class = "schedule schedule_item facility_" . $reservation->facility->id;
                    $data  = "data-object='reservation' data-object_id=" . $reservation->id;
    
                    $order = $orders[$date_text]+1;
                    $orders[$date_text]++;
                    $style = $reservation->style();
                    
                    @endphp
                    <div class="col0{{ $col }} span01 cal3" style="{{ $row_style }}">
                        <div class="calendar_item order{{ $order }} {{ $class }} {{ $complete }} single_schedule object_to_show_detail d-flex" style="{{ $style }}" {!! $data !!}> {{-- htmlspecialchars OK --}}
                            <span class="flex-fill text-left text-truncate">{{ $reservation->p_time('monthly') }}</span>
                            <span class="flex-fill text-right text-trunctate pr-2">{{ $reservation->user->name }}</span>
                        </div>
                    </div>
                @endif
            @endforeach
        @endforeach

        @php
        $i++;
        #if_debug( $orders );

        @endphp
    @endforeach
    
    
    
</div>
