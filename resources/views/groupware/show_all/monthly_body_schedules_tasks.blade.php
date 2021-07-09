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
    $orders[$d] = 0;
}

$start_date_of_calendar = Arr::first( $returns['dates'] );
$end_date_of_calendar   = Arr::last(  $returns['dates'] );

$others = [];  // 表示しきれずその他何件と表示するための配列（キー　日付、値　その他の件数）

@endphp

{{-- 複数日の予定を表示 --}}

@foreach( $returns['multi'] as $i => $schedule )
    @php
    
        // $start = $schedule->start->copy();
        // $end   = $schedule->end->copy();
        
        $start = new Carbon( $schedule->start->format( 'Y-m-d 00:00:00' ));
        $end   = new Carbon( $schedule->end->format(   'Y-m-d 23:59:59' ));
        
        //　開始日、終了日を表示カレンダーの中に収める
        //
        #while( $start->lt( $start_date_of_calendar )) {
        #    $start->addDay();
        #}
        if( $start->lt( $start_date_of_calendar )) { $start = $start_date_of_calendar->copy(); }
        
        #while( $end->gt( $end_date_of_calendar )) {
        #while( $end->diffInDays( $end_date_of_calendar ) > 1 ) {
        #    $end->subDay();
        #}
        if( $end->gt( $end_date_of_calendar )) { $end = $end_date_of_calendar->copy(); }
        
        $start_row = $rows[ $start->format( 'Y-m-d' ) ];
        $start_col = $cols[ $start->format( 'Y-m-d' ) ];
        $end_row   = $rows[ $end->format( 'Y-m-d' ) ];
        $end_col   = $cols[ $end->format( 'Y-m-d' ) ];
        
        $style = $schedule->style();
        
        $span = $end->diffInDays( $start ) + 1;
        #if( $schedule->name == "グアム旅行" ) {     if_debug( $start, $end, $span ); }
    
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
        
        $schedule_class = "schedule schedule_item calendar_" . $schedule->calendar_id;
        // $data_schedule = " data-schedule_id='$schedule->id' data-calendar_id='$schedule->calendar_id' ";
        $data = "data-object='schedule' data-object_id=" . $schedule->id;
        
        @endphp
    
        @if( $order >= 6 and ( $d->isSaturday() or $d->diffInDays( $end ) == 0 )) 
            {{-- その他〇〇件 --}}

        @elseif( $d->isSaturday() ) 
            @php
            $style = $schedule->style();
            @endphp
        
            <div class="row{{ $start_row }} col{{ $start_col }} span{{ $span }} cal3" style="pointer-events: none;">
                <div class="calendar_item order{{ $order }} {{ $schedule_class }} multi_schedule object_to_show_detail" style="{{ $style }}" {!! $data !!}> {{-- htmlspecialchars OK --}}
                    @if( $user_id != $schedule->user_id ) 【{{ $schedule->user->name }}】 @endif
                
                    {{ $schedule->name }}
                    @if( ! $schedule->all_day )
                        {{ $schedule->start->format( 'H:i' ) }}
                    @endif
                </div>
            </div>
            @php
                $start = $d->copy()->addDay();
            @endphp
        @elseif( $d->diffInDays( $end ) == 0 )
            @php
            $style = $schedule->style();
            @endphp
            <div class="row{{ $start_row }} col{{ $start_col }} span{{ $span }} cal3" style="pointer-events: none;">
                <div class="calendar_item order{{ $order }} multi_schedule {{ $schedule_class }} order{{ $order }} object_to_show_detail" style="{{ $style }}" {!! $data !!}> {{-- htmlspecialchars OK --}}
                    @if( $user_id != $schedule->user_id ) 【{{ $schedule->user->name }}】 @endif
                    {{ $schedule->name }}
                </div>
            </div>
        @endif

        @php
        $d->addDay();
        @endphp
    @endwhile
@endforeach

{{-- 終日予定、タスク、１日以内の予定　を表示 --}}

@foreach( [ $returns['single'], $returns['time'], $returns['task'] ] as $return_values )
    @foreach( $return_values as $Item )

        @if( $Item instanceof Schedule )    
            @php
            $schedule = $Item;
            
            $start = $schedule->start->copy();
            $d_date = $start->format( 'Y-m-d' );
            
            $start_row = $rows[ $d_date ];
            $start_col = $cols[ $d_date ];
            
            $style = $schedule->style();
        
            $order = $orders[$d_date] + 1;
            $orders[$d_date] = $order;
            
            $schedule_class = "schedule schedule_item calendar_" . $schedule->calendar_id;
            // $data_schedule = " data-schedule_id='$schedule->id' data-calendar_id='$schedule->calendar_id' ";
            $data = "data-object='schedule' data-object_id=" . $schedule->id;
            
            @endphp
            
            <div class="row{{ $start_row }} col{{ $start_col }} span1 cal3" style="pointer-events: none;">
                <div class="calendar_item order{{ $order }} {{ $schedule_class }} single_schedule object_to_show_detail" style="{{ $style }}" {!! $data !!}> {{-- htmlspecialchars OK --}}
                    @if( $order < 6 )
                        @if( $user_id != $schedule->user_id ) 【{{ $schedule->user->name }}】 @endif
                        {{ $schedule->name }} 
                        {{ $schedule->p_time( 'monthly' ) }} 
                    @else
                        {{-- その他〇〇件 --}}
                        @php
                        $others[$d_date] = op( $others )[$d_date] + 1;
                        @endphp
                    @endif
                </div>
            </div>
            
        {{-- タスクを表示 --}}
        
        @elseif( $Item instanceof Task )
            @php
            $task = $Item;
            
            $d_date = $task->due_time->format( 'Y-m-d' );
            
            $start_row = $rows[ $d_date ];
            $start_col = $cols[ $d_date ];      
            
            $style = $task->style();
        
            $order = $orders[$d_date] + 1;
            $orders[$d_date] = $order;

            $complete   = ( $task->status == "完了" ) ? "task_complete" : "";

            $task_class = "task task_item tasklist_" . $task->tasklist_id;
            // $data_task  = " data-task_id='$task->id' data-tasklist_id='$task->tasklist_id' ";
            $data = "data-object='task' data-object_id=" . $task->id;

            @endphp
            <div class="row{{ $start_row }} col{{ $start_col }} span1 cal3" style="pointer-events: none;">
                <div class="calendar_item order{{ $order }} {{ $task_class }} {{ $complete }} single_schedule object_to_show_detail" {!! $data !!} style="{{ $style }}">
                    @if( $order < 6 )
                        @if( $task->status == "完了" )
                            @icon( check )
                        @else
                             @icon( check-circle-r )
                        @endif
                        @if( $user_id != $task->user_id ) 【{{ $task->user->name }}】 @endif
                        {{ $task->name }}
                        {{ $task->p_time('daily') }}
                    @else
                        {{-- その他〇〇件 --}}
                        @php
                        $others[$d_date] = op( $others )[$d_date] + 1;
                        @endphp
                    @endif
                </div>
            </div>
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
        <div class="date_item calendar_item order6 single_schedule other_item" data-date="{{ $d_date }}">
            その他 {{ $num }} 件・・・
        </div>
    </div>    
@endforeach

</div>

