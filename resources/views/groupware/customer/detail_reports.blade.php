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
        <div class="card-body m-2 table table-border table-sm" id="report_list">
            <div class="row">
                <div class="col-5 d-none d-lg-block font-wieght-bold">件名</div>
                <div class="col-5 d-none d-lg-block font-wieght-bold">日付</div>
                <hr  class="col-12 d-none d-lg-block">
                @php
                $date = "";
                @endphp
                
                @foreach( $reports as $report ) 
                    @php
                    $style = $report->style();
                    @endphp
                
                    <div class="col-5 btn object_to_show_detail text-truncate text-left" data-object='report' data-object_id={{ $report->id }}>
                        @icon( clipboard ) {{ $report->name }}
                    </div>
                    <div class="col-5 btn object_to_show_detail text-truncate text-left" data-object='report' data-object_id={{ $report->id }}>
                        {{ $report->p_time() }}                    
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@else
    <div class="col-12">日報はありません</div>
@endif

