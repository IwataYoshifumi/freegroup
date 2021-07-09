@php

#if_debug( $schedules );

@endphp

<div class="col-12">&nbsp;</div>
@if( count( $schedules ))             
    <div class="card">
        <div class="card-header">
            <div class="row">
                <span class="col   btn btn_icon text-left" id="btn_toggle_schedule_item">@icon( caret-square-down )  今後の予定　＆　過去１年の予定</span>
            </div>
            <script>
                $('#btn_toggle_schedule_item').on( 'click', function() {
                     $('#schedule_list').toggle( 'blind', 100 );
                });
            </script>
            
        </div>
        <table class="card-body m-2 table table-border table-sm" id="schedule_list">
            <tr>
                <th>件名</th>
                <th>月日</th>
                <th>時間</th>
            </tr>
            
            @php
            $date = "";
            @endphp
            
            @foreach( $schedules as $schedule ) 
                @php
                $style = $schedule->style();
                @endphp
            
                <tr class="show_modal_detail_object date_item" data-object_type='schedule' data-object_id={{ $schedule->id }}>
                    <td style="">
                        <span>
                            @icon( schedule ) {{ $schedule->name }}
                        </span>
                    </td>
                    <td>
                        @if( $date != $schedule->start->format( 'Y-m-d' )) 
                            {{ $schedule->start->format( 'n月j日' ) }}【{{ p_date_jp( $schedule->start->format('w') ) }}】               
                        @endif
                        @if( $schedule->end->diffInDays( $schedule->start ) >= 1 )
                            ～{{ $schedule->end->format( 'n月j日' ) }}【{{ p_date_jp( $schedule->end->format('w') ) }}】
                        @endif
                    </td>
                    <td>
                        @if( $schedule->all_day ) 
                            終日
                        @else
                            {{ $schedule->start->format( 'G:i' ) }}～ {{ $schedule->end->format( 'G:i' ) }}
                        @endif
                    </td>
                </tr>
            @endforeach
            </div>
        </table>
    </div>
@else
    <div class="col-12">本日以降の予定はありません</div>
@endif

