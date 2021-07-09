@php

#if_debug( $reports );

@endphp

<div class="col-12">&nbsp;</div>
@if( count( $reports ))             
    <div class="card">
        <div class="card-header">
            <div class="row">
                <span class="col   btn btn_icon text-left" id="btn_toggle_report_item">@icon( caret-square-down )  直近の関連日報</span>
            </div>
            <script>
                $('#btn_toggle_report_item').on( 'click', function() {
                     $('#report_list').toggle( 'blind', 100 );
                });
            </script>
            
        </div>
        <table class="card-body m-2 table table-border table-sm" id="report_list">
            <tr>
                <th>件名</th>
                <th>月日</th>
                <th>時間</th>
            </tr>
            
            @php
            $date = "";
            @endphp
            
            @foreach( $reports as $report ) 
                @php
                $style = $report->style();
                @endphp
            
                <tr class="" data-object_type='report' data-object_id={{ $report->id }}>
                    <td style="">
                        @php
                        $url = route( 'groupware.report.show', [ 'report' => $report->id ] );
                        @endphp
                        <a href="{{ $url }}" class="btn ">
                            @icon( clipboard ) {{ $report->name }}
                        </a>
                    </td>
                    <td>
                        @if( $date != $report->start->format( 'Y-m-d' )) 
                            {{ $report->start->format( 'n月j日' ) }}【{{ p_date_jp( $report->start->format('w') ) }}】               
                        @endif
                        @if( $report->end->diffInDays( $report->start ) >= 1 )
                            ～{{ $report->end->format( 'n月j日' ) }}【{{ p_date_jp( $report->end->format('w') ) }}】
                        @endif
                    </td>
                    <td>
                        @if( $report->all_day ) 
                            終日
                        @else
                            {{ $report->start->format( 'G:i' ) }}～ {{ $report->end->format( 'G:i' ) }}
                        @endif
                    </td>
                </tr>
            @endforeach
            </div>
        </table>
    </div>
@else
    <div class="col-12">日報はありません</div>
@endif

