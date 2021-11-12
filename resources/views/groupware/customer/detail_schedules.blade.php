@php

#if_debug( $schedules );

@endphp

<div class="col-12">&nbsp;</div>
@if( count( $schedules ))             
    <div class="card">
        <div class="card-header">
            <div class="row">
                <span class="col   btn btn_icon text-truncate text-left" id="btn_toggle_schedule_item">@icon( caret-square-down )  今後と過去１年の予定</span>
            </div>
            <script>
                $('#btn_toggle_schedule_item').on( 'click', function() {
                     $('#schedule_list').toggle( 'blind', 100 );
                });
            </script>
            
        </div>
        <div class="card-body m-2 table table-border table-sm" id="schedule_list">
            <div class="row">
                <div class="d-none d-lg-block col-6 text-truncate font-weight-bold ">件名</div>
                <div class="d-none d-lg-block col-6 text-truncate font-weight-bold ">日時</div>
                <hr  class="d-none d-lg-block col-12">
                
                @php
                $date = "";
                @endphp
                
                @foreach( $schedules as $schedule ) 
                    @php
                    $style = $schedule->style();
                    @endphp
                
                    <div class="col-6 text-truncate text-left btn object_to_show_detail date_item" data-object='schedule' data-object_id={{ $schedule->id }}>
                        @icon( schedule ) {{ $schedule->name }}
                    </div>
                    <div class="col-6 text-truncate text-left btn object_to_show_detail date_item" data-object='schedule' data-object_id={{ $schedule->id }}>
                        {{ $schedule->p_time( 'index' ) }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@else
    <div class="col-12">本日以降の予定はありません</div>
@endif

