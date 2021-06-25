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
                <div class="col-4">スケジュール・タスク</div>
                <div class="col-6">日時</div>
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
                        @foreach( [ $returns['multi'], $returns['single'], $returns['time'], $returns['task'] ] as $items )
                            @foreach( $items as $item )
                                @if( $item->user_id != $user_id ) @continue @endif
                                @php
                                $style = $item->style();
                                if( $item instanceof Schedule ) {
                                    $url = route( 'groupware.schedule.show', [ 'schedule' => $item->id ] );
                                    $data = "data-class='schedule' data-id=" . $item->id;
                                } else {
                                    $url = route( 'groupware.task.show', [ 'task' => $item->id ] );
                                    $data = "data-class='task' data-id=" . $item->id;
                                }
                                @endphp
                            
                                <div class="event_item col-4" {!! $data !!}>
                                    <a href="{{ $url }}" class="btn btn-sm text-left w-100" style="{{ $style }}">
                                        @if( $item instanceof Schedule) @icon(calendar) @else @icon( check-circle-r )  @endif
                                        {{ $item->name }}
                                    </a>
                                </div>
                                <div class="col-6">{{ $item->p_time_in_daily_form() }}</div>
                            @endforeach
                        @endforeach
                    </div>
                </td>
            @endforeach
        </tr>
    @endforeach
</table>
            