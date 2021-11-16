@php

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Http\Helpers\MyForm;
use App\Http\Helpers\OutputCSV;
use App\Http\Helpers\ScreenSize;
use Carbon\Carbon;

use App\Models\Customer;

use App\myHttp\GroupWare\Models\Schedule;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;
use App\myHttp\GroupWare\Models\Task;
use App\myHttp\GroupWare\Models\TaskProp;
use App\myHttp\GroupWare\Models\TaskList;
use App\Http\Helpers\BackButton;

$user_id = user_id();

$sidebar_height = 30;

$depts = $returns['depts'];  
$users = $returns['users'];
$dept_user = $returns['dept_user'];

setlocale(LC_ALL, 'ja_JP.UTF-8');
#dd( $request->all() );

if_debug( $request->all(),$returns );

$j = 0;
@endphp

<div class="row no-gutters w-100" style="">
    @foreach( $depts as $dept_id => $dept )
        @php
        $users = $dept_user[$dept_id];
        $num_users = count( $dept_user[$dept_id] );
        $row_span  = count( $dept_user[$dept_id] );
        @endphp
        @foreach( $users as $user_id => $user )
            @php
            $user = $users[$user_id];
            $objects = $returns[$user_id];
            @endphp

            @if( $j ) <div class="col-12">&nbsp;</div> @endif
            <div class="col-12 h5 shadow p-1">{{ $dept->name }} {{ $user->name }} {{ $user->grade }}</div>
            
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
                $style_complete = "";

                @endphp
            
                @if( $object instanceof Task or ( $object instanceof Schedule and $object->all_day ))
                    {{--
                      --
                      -- タスク、予定（複数日、終日）の表示
                      --
                      --}}
                    <div class="object_to_show_detail event_item btn col-12 d-flex justify-content-start" {!! $data !!}>
                        <div class="w-10 object_to_show_detail mr-2 ml-1" {!! $data !!}>
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
                        <div class="w-50 text-truncate text-left" style="{{ $style }} {{ $style_complete }}">

                            {{ $object->name }}
                        </div>
                        <div class="text-truncate text-left ml-1">
                            {{ $object->p_time( 'daily' ) }}
                            @if( $object->user_id != $user->id )
                                <span class="uitooltip" title="関連社員登録">@icon( users )</span>
                            @endif
                        </div>
                    </div>
                @elseif( $object instanceof Schedule and ! $object->all_day )
                    {{--
                      --
                      -- １日の終日でない予定
                      --
                      --}}
                      
                    <div class="object_to_show_detail event_item btn col-12 d-flex justify-content-start" {!! $data !!}>
                        <div class="w-10 object_to_show_detail mr-2 ml-1" {!! $data !!}>
                            @if( $object instanceof Schedule) @icon(calendar) @else @icon( check-circle-r )  @endif
                        </div>
                        <div class="w-50 text-truncate text-left" style="{{ $style }}">
                                {{ $object->name }}
                        </div>
                        <div class="text-truncate text-left ml-1">

                        {{ $object->p_time_for_daily_form() }}
                        </div>
                        @if( $object->user_id != $user->id ) 
                            <span class="uitooltip" title="関連社員登録">@icon( users )</span>
                        @endif
                    </div>
                @endif
                @php
                $j++;
                @endphp

            @endforeach {{-- loop objects --}}
        @endforeach  {{-- loop user --}}
    @endforeach  {{-- loop dept --}}
</div>

<script>
    // 
    // 詳細表示ダイヤログ（親スクリプトを実行）
    // 
    $('.object_to_show_detail').on( 'click', function() {
         window.parent.click_object_to_show_detail( $(this) );
    });

    //
    // モバイル用に表示調整
    //
    @if( ScreenSize::isMobile() ) 
    $('.object_to_show_detail').css( 'font-size', 'x-small' );
    $('.object_to_show_detail').css( 'padding', '0' );
    @endif

</script>
