@php

#if_debug( $schedules );

@endphp

<div class="col-12 d-none d-lg-block">&nbsp;</div>

@if( count( $schedules ))             
    <div class="card">
        <div class="card-header p-1 p-lg-20">
            <div class="row">
                <span class="col   btn btn_icon text-left" id="btn_toggle_schedule_item">@icon( caret-square-down ) 本日の予定</span>
                <span class="col-1 btn btn_icon ml-auto d-none d-lg-block" onClick="location.reload();">@icon( sync ) </span>
            </div>
            <script>
                $('#btn_toggle_schedule_item').on( 'click', function() {
                     $('#schedule_list').toggle( 'blind', 100 );
                });
            </script>
            
        </div>
        <div class="m-1" id="schedule_list">
            <div class="row no-gutters">
                <div class="col-7  d-none d-lg-block font-weight-bold">件名</div>
                <div class="col-4  d-none d-lg-block font-weight-bold">日時</div>
                <hr  class="col-12 d-none d-lg-block m-0 mb-1">
                
                @php
                $date = "";
                @endphp
                
                @foreach( $schedules as $schedule ) 
                    @php
                    $style = $schedule->style();
                    @endphp
                    <div class="object_to_show_detail date_item text-truncate col-7" data-object='schedule' data-object_id={{ $schedule->id }}>
                            @icon( schedule ) {{ $schedule->name }}
                    </div>
                    <div class="object_to_show_detail date_item text-truncate col-4" data-object='schedule' data-object_id={{ $schedule->id }}>
                        {{ $schedule->p_time_for_daily_form() }}
                        {{--
                        @if( $schedule->all_day ) 
                            終日
                        @else
                            {{ $schedule->start->format( 'G:i' ) }}～ {{ $schedule->end->format( 'G:i' ) }}
                        @endif
                        --}}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@else
    <div class="col-12">本日の予定はありません</div>
@endif

