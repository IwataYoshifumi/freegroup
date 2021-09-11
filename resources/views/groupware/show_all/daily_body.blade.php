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

if_debug( $request->all() );
#dd( $users, $depts, $dept_user );

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
                <div class="col-7">
                    日時
                    @if( $request->search_users )
                        （ @icon(users) 関連社員 ）
                    @endif
                </div>
            </div>
        </th>
    </tr>
    @foreach( $depts as $dept_id => $dept )
        @php
        $users = $dept_user[$dept_id];
        $num_users = count( $dept_user[$dept_id] );
        $row_span  = count( $dept_user[$dept_id] );
        @endphp
        <tr>
            <td class="" rowspan={{ $row_span }} style="writing-mode: vertical-lr">{{ $dept->name }}</td>
            @foreach( $users as $user_id => $user )
                @php
                $user = $users[$user_id];
                $objects = $returns[$user_id];
                @endphp
                <td class="text-nowrap">{{ $user->name }} {{ $user->grade }}</td>
                
                <td>
                    <div class="row">
                        {{--
                        @foreach( [ $returns['multi'], $returns['single'], $returns['time'], $returns['task'] ] as $items )
                        @foreach( [ $returns['task'], $returns['all_day'], $returns['multi_not_all_day'], $returns['time'] ] as $items )
                        --}}
                        @foreach( Arr::collapse( [ $objects['task'], $objects['multi'], $objects['single'], $objects['time'] ] ) as $object )
                            @if( 0 and $object->user_id != $user_id ) @continue @endif
                            @php
                            $style = $object->style();
                            
                            if( $object instanceof Schedule ) {
                                $url = route( 'groupware.schedule.show', [ 'schedule' => $object->id ] );
                                $data = "data-object='schedule' data-object_id=" . $object->id;
                            } else {
                                $url = route( 'groupware.task.show', [ 'task' => $object->id ] );
                                $data = "data-object='task' data-object_id=" . $object->id;
                            }
                            @endphp
                        
                            @if( $object instanceof Task or ( $object instanceof Schedule and $object->all_day ))
                                {{--
                                  --
                                  -- タスク、予定（複数日、終日）の表示
                                  --
                                  --}}
                                @php
                                $style_complete = "";
                                @endphp
                                  
                                <div class="event_item col-5" {!! $data !!}>
                                    <div class="row">
                                        <div class="col-1 mr-2">
                                            @if( $object instanceof Schedule) 
                                                @icon(calendar) 
                                            @else 
                                                @if( $object->isComplete() ) 
                                                    @icon(check) 
                                                    @php
                                                    $style_complete = "text-decoration: line-through"; 
                                                    @endphp
                                                @else 
                                                    @icon( check-circle-r ) 
                                                @endif 
                                            @endif
                                        </div>

                                        <span class="col btn btn-sm text-left w-100 object_to_show_detail" {!! $data !!} style="{{ $style }} {{ $style_complete }}">
                                            {{ $object->name }}

                                        </span>
                                    </div>
                                </div>
                                <div class="col-7">{{ $object->p_time( 'daily' ) }}
                                    <span class="uitooltip" title="関連社員">
                                        @if( $object->user_id != $user->id ) @icon( users ) @else &nbsp; @endif
                                    </span>
                                </div>
                            @elseif( $object instanceof Schedule and ! $object->all_day )
                                {{--
                                  --
                                  -- １日の終日でない予定
                                  --
                                  --}}
                                <div class="event_schedule col-5" {!! $data !!}>
                                    <div class="row">
                                        <div class="col-1 mr-2">
                                            @if( $object instanceof Schedule) @icon(calendar) @else @icon( check-circle-r )  @endif
                                        </div>
                                        <span class="col btn btn-sm text-left w-100 object_to_show_detail" {!! $data !!}  style="{{ $style }}">
                                            {{ $object->name }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-7">
                                    {{ $object->p_time_for_daily_form() }}
                                    <span class="uitooltip" title="関連社員">
                                        @if( $object->user_id != $user->id ) @icon( users ) @else &nbsp; @endif
                                    </span>
                                </div>
                            @endif


                        @endforeach
                        
                    </div>
                </td>
            </tr>                
            @endforeach  {{-- loop user --}}
            
    @endforeach  {{-- loop dept --}}
</table>

<!-- スケジュール詳細ダイアログ -->
<!--include( 'groupware.show_all.modal_to_show_detail' )-->
@include( 'groupware.show_all.dialog.show_detail' )