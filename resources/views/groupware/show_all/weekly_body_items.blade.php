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
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;

use App\myHttp\GroupWare\Controllers\Search\SearchSchedule;
use App\myHttp\GroupWare\Controllers\Search\SearchSchedulesAndTasks;
use App\myHttp\GroupWare\Controllers\Schedule2IndexController;

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

$dates = $returns['dates'];
$depts = $returns['depts'];
$users = $returns['users'];


@endphp

<div >
    @php
    $i = 1;
    $top = 0;
    $next_top = 0;

    $max_objects = []; // ユーザ別、表示高さ調整用（キー User ID, 値　最大表示オブジェクト数）

    $template_orders = [];
    foreach( $dates as $date ) {
        $tmplate_orders[$date->format('Y-m-d')] = 0;
    }
    
    @endphp

    @foreach( $depts as $dept )
        @php 

        @endphp
    
        @foreach( $users as $user )
            @if( $user->dept_id != $dept->id ) @continue @endif
            @php
            
            $orders = $tmplate_orders;

            $max = $returns[$user->id]['max_num_of_objects'] + 1;
            $height = 28 + $max * 20;
            $top = $next_top;

            $next_top = $top + $height;
            $row_style = "top: " . $top . "px; height: " . $height . "px;"; // ユーザごとの行の高さ、トップの位置
            
            @endphp
            
            <div class="col01 span01 cal3 border border-dark bg-white row_style_user_{{ $user->id }}" style="{{ $row_style }}">
                <div class="order1 font-weight-bold">
                    {{ $dept->name }}<br>
                    {{ $user->name }} {{ $user->grade }} @if( is_debug() ) {{ $user->id }} @endif
                </div>
            </div>
            
            @foreach( $dates as $date )
                @php
                $date_text = $date->format('Y-m-d');
                $col = $returns['cols'][$date_text] + 1;
                
                $items = $returns[$user->id][$date_text];
                #if_debug( $items );
                
                if( $date->eq( $today )) {
                    $box_class = "today_box";
                } elseif( $date->month != $base_date->month ) {
                    $box_class = "other_month_box";
                } else {
                    $box_class = "bg-white";
                }
            
                @endphp
            
                <div class="col0{{ $col }} span01 cal3 border border-dark {{ $box_class }} row_style_user_{{ $user->id }}" style="{{ $row_style }}">
                    <div class="order0 date_item" data-date="{{ $date_text }}">
                        &nbsp; {{ $date->format( 'n/j' ) }}
                    </div>
                </div>

                {{--
                  --
                  --  複数日の予定
                  --
                  --}}
                @foreach( $items['multi'] as $schedule )
                    @if( $schedule instanceof Schedule )
                        @php
                        $tmp_d      = $date->copy();
                        $start_date = $date->copy();

                        $order = $orders[$date->format('Y-m-d')] + 1;
                        $orders[$date->format('Y-m-d')] = $order;

                        for( $j = 1; $j <= 7; $j++ ) {
                            // if( $tmp_d->gte( $schedule->end ) or $tmp_d->isSaturday() ) { break; }
                            // if( $tmp_d->gte( $schedule->end ) or ( $j > 1 and $tmp_d->isSunday() )) { break; }
                            if( subInDays( $tmp_d, $schedule->end ) >= 0 or ( $tmp_d->isSaturday() )) { break; }
                            $orders[$tmp_d->format('Y-m-d')] = $order;
                            $tmp_d->addDay();                                
                        }

                        $orders[$tmp_d->format('Y-m-d')] = $order;
                        
                        #$span = $start_date->diffInDays( $tmp_d ) + 1;
                        $span = diffInDays( $start_date, $tmp_d ) + 1;
                        
                        $schedule_class = "schedule schedule_item calendar_" . $schedule->calendar_id;
                        $data = "data-object='schedule' data-object_id=" . $schedule->id;
                        $style = $schedule->style();
                        @endphp
                        <div class="col0{{ $col }} span0{{ $span }} cal3  row_style_user_{{ $user->id }}" style="{{ $row_style }}">
                            <div class="w-100 cal3 calendar_item order{{ $order }} {{ $schedule_class }} object_to_show_detail" style="{{ $style }}" {!! $data !!}> {{-- htmlspecialchars OK --}}
                                <div class="d-flex w-100">
                                    <div class="text-left text-truncate">
                                        {{ $schedule->name }} 
                                    </div>
                                    <div class="flex-fill text-right">
                                        @if( ! $schedule->all_day )
                                            {{ $schedule->start->format('h:i') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else 
                        @php
                        #$orders[$date->format('Y-m-d')]++;
                        @endphp
                    @endif
                    
                @endforeach
                {{--
                  --
                  -- １日の予定、タクスを表示
                  --
                  --}}
                @foreach( Arr::collapse( [ $items['single'], $items['time'], $items['task'] ] ) as $item ) 
                    
                    @php
                    $complete = "";
                    if( $item instanceof Schedule ) {
                        $class = "schedule schedule_item calendar_" . $item->calendar_id;
                        $data  = "data-object='schedule' data-object_id=" . $item->id;
                    } elseif( $item instanceof Task ) {
                        $class = "schedule schedule_item tasklist_" . $item->tasklist_id;
                        $data  = "data-object='task' data-object_id=" . $item->id;
                        $complete   = ( $item->status == "完了" ) ? "task_complete" : "";
                    } 
                    $order = $orders[$date_text]+1;
                    $orders[$date_text] = $order;
                    $style = $item->style();
                    
                    @endphp
                    <div class="col0{{ $col }} span01 cal3 row_style_user_{{ $user->id }}" style="{{ $row_style }}">
                        <div class="calendar_item order{{ $order }} {{ $class }} {{ $complete }} single_schedule object_to_show_detail d-flex" style="{{ $style }}" {!! $data !!}> {{-- htmlspecialchars OK --}}
                            <div class="text-left text-truncate">
                                @if( $item instanceof Task ) 
                                    @if( $item->status == "完了" )
                                        @icon( check )
                                    @else
                                         @icon( check-circle-r )
                                    @endif
                                @endif
                                {{ $item->name }}
                            </div>
                            <div class="flex-fill text-right">
                                @if( ! $item->all_day and method_exists( $item, 'p_time' ))
                                    <span>{{ $item->p_time('monthly') }}</span>
                                @else 
                                    <span>終日</span>
                                @endif
                            </div>
                        </div>
                    </div>
                
                    @if( 0 and $user->id == 2 )
                        @php
                        if_debug( $user->id,  $item, $item->users );
                        @endphp
                    @endif
                @endforeach
            @endforeach

            @php
            $i++;

            // 各ユーザの表示高さの調整用（最後のJavaScriptで仕様）
            //
            $max_objects[$user->id] = max( $orders );

            @endphp
        @endforeach  {{-- Loop Users --}}
    @endforeach  {{-- Loop Depts --}}
    
     <script>
        //　各ユーザごとの表示幅を調整するスクリプト
        //
        $(document).ready( function() {
            @php
            $top = 0;
            $next_top = 0;
            @endphp

            @foreach( $max_objects as $user_id => $max ) 
                @php
                $max++;
                $height = 28 + $max * 20;
                $top      = $next_top;
                $next_top = $top + $height;
                @endphp
                
                $('.row_style_user_{{ $user_id }}').css( 'top',    {{ $top }} );
                $('.row_style_user_{{ $user_id }}').css( 'height', {{ $height }} );
                {{-- 
                console.log( {{ $user_id }},  {{ $max}},  {{ $top }}, {{ $height }} );
                --}}
            @endforeach
        });
        
    </script>
    
</div>
