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
use App\myHttp\GroupWare\Models\TaskProp;
use App\myHttp\GroupWare\Models\TaskList;

$user_id = user_id();

$sidebar_height = 30;

$base_date = new Carbon( $request->base_date );
$today = new Carbon( 'today' );

$depts = $returns['depts'];  
$users = $returns['users'];
$dept_user = $returns['dept_user'];

$route_name = Route::currentRouteName();

setlocale(LC_ALL, 'ja_JP.UTF-8');
#dd( $request->all() );

@endphp

@include( 'groupware.show_all.daily_button' )

<br>
<table class="table">
    <tr>
        <th>部署</th>
        <th>社員名</th>
        <th>
            <div class="row">
                <div class="col-5">スケジュール・タスク</div>
                <div class="col-7">日時</div>
            </div>
        </th>
    </tr>
    @foreach( $depts as $dept_id => $dept )
        @php
        $num_users = count( $dept_user[$dept_id] );
        $row_span = count( $dept_user[$dept_id] );
        @endphp
        <tr>
            <td class="" rowspan={{ $row_span }}>{{ $dept->name }}</td>
            @foreach( $dept_user[$dept_id] as $user_id )
                @php
                $user = $users[$user_id];
                @endphp
                <td>{{ $user->name }}</td>
                
                <td>
                    <div class="row">
                        {{--
                        @foreach( [ $returns['multi'], $returns['single'], $returns['time'], $returns['task'] ] as $items )
                        @foreach( [ $returns['task'], $returns['all_day'], $returns['multi_not_all_day'], $returns['time'] ] as $items )
                        --}}
                        @foreach( [ $returns['task'], $returns['all_day'] ] as $items )
                            @foreach( $items as $item )
                                @if( $item->user_id != $user_id ) @continue @endif
                                @php
                                $style = $item->style();
                                
                                if( $item instanceof Schedule ) {
                                    $url = route( 'groupware.schedule.show', [ 'schedule' => $item->id ] );
                                    $data = "data-object='schedule' data-object_id=" . $item->id;
                                } else {
                                    $url = route( 'groupware.task.show', [ 'task' => $item->id ] );
                                    $data = "data-object='task' data-object_id=" . $item->id;
                                    // if( $item->status == "完了" ) { $style .= " text-decoration: line-through;"; }
                                }
                                @endphp
                            
                                <div class="event_item col-5" {!! $data !!}>
                                    <div class="row">
                                        <div class="col-1 mr-2">
                                            @if( $item instanceof Schedule) @icon(calendar) @else @icon( check )  @endif
                                        </div>
                                        <span class="col btn btn-sm text-left w-100 object_to_show_detail" {!! $data !!} style="{{ $style }}">
                                            {{ $item->name }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-7">{{ $item->p_time( 'daily' ) }}</div>
                            @endforeach
                        @endforeach
                        
                        {{--
                          --
                          --
                          -- 終日でない予定
                          --
                          --}}
                        @foreach( $returns['time_for_daily'] as $time => $schedules )
                            @foreach( $schedules as $i => $schedule )
                                @if( $schedule->user_id != $user_id ) @continue @endif
                                @php

                                $num_dates = $schedule->getNumDates();

                                $style = $schedule->style();
                                $url = route( 'groupware.schedule.show', [ 'schedule' => $schedule->id ] );
                                $data = "data-object='schedule' data-object_id=" . $schedule->id;
                                @endphp
                                <div class="event_schedule col-5" {!! $data !!}>
                                    <div class="row">
                                        <div class="col-1 mr-2">
                                            @if( $schedule instanceof Schedule) @icon(calendar) @else @icon( check-circle-r )  @endif
                                        </div>
                                        <span class="col btn btn-sm text-left w-100 object_to_show_detail" {!! $data !!}  style="{{ $style }}">
                                            {{ $schedule->name }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-7">
                                    {{ $schedule->p_time_for_daily_form() }}
                                </div>
                            @endforeach
                        @endforeach 
                    </div>
                </td>
        </tr>                
            @endforeach  {{-- loop user --}}
            
    @endforeach  {{-- loop dept --}}
</table>

@include( 'groupware.show_all.modal_to_show_detail' )
            