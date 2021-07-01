@php
use Illuminate\Support\Facades\Route;

$current_route         = Route::currentRouteName();

@endphp

<div class="cal3 top_area border border-dark" style="background-color: peachpuff" id="top_area">
    <div class="row no-gutters">
        <div class="col-2 w-10 mr-auto">
            <div class="row">

                {{--
                  --
                  --
                  --  新規ボタン・検索ボタン
                  --
                  --
                  --}}
                @php

                $route_create_schedule = route( 'groupware.schedule.create' );
                $route_create_task     = route( 'groupware.task.create'     );
                $route_create_report   = route( 'groupware.report.create'   );

                @endphp
                
                <div class="col btn ml-2 btn_icon" id="sidebar_opener">@icon( angle-double-right )</div>
                <a class="col btn btn_icon text-primary uitooltip" title="スケジュール作成" href="{{ $route_create_schedule }}">@icon( schedule     )</a>
                <a class="col btn btn_icon text-primary uitooltip" title="タスク作成"       href="{{ $route_create_task     }}">@icon( check-circle )</a>
                <a class="col btn btn_icon text-primary uitooltip" title="日報作成"         href="{{ $route_create_report   }}">@icon( clipboard    )</a>
                
                @if( $current_route == "groupware.show_all.monthly" )
                    <a class="col btn btn_icon uitooltip" title="週表示" id="switch_show_all_btn" data-action="{{ route( 'groupware.show_all.weekly' ) }}">週</a>
                @elseif( $current_route == "groupware.show_all.weekly" )
                    <a class="col btn btn_icon uitooltip" title="月表示" id="switch_show_all_btn" data-action="{{ route( 'groupware.show_all.monthly' ) }}">月</a>                
                @endif
                <script>                
                    $("#switch_show_all_btn").on( 'click', function() {
                        console.log( 'aaa' );
                        var search_form = $('#search_form');
                        search_form.attr( 'action', $(this).data('action') );
                        search_form.submit();
                    });
                </script>

                
                
            </div>
        </div>
        <div class="col-3">
            <div class="row">
                @php
                if( $request->span == "monthly" ) {
                    $previous_date = $base_date->copy()->subMonth()->format( 'Y-m-d' );
                    $next_date     = $base_date->copy()->addMonth()->format( 'Y-m-d' );
                    $date_title    = $base_date->format( 'Y年 n月' );
                } elseif( $request->span == "weekly" ) {
                    $previous_date = $base_date->copy()->subDays( 7 )->format( 'Y-m-d' );
                    $next_date     = $base_date->copy()->addDays( 7 )->format( 'Y-m-d' );
                    $d             = Arr::first( $returns['dates'] );
                    // dump( $d, $d->weekNumberInMonth, $base_date->weekNumberInMonth );
                    $date_title    = $base_date->format( 'Y年n月' ) . $base_date->weekNumberInMonth. "週目";
                }
                @endphp


                {{--
                  --
                  --
                  --　年月表示、月切替ボタン
                  --
                  --}}
                <div class="col btn btn_icon month_button" data-date="{{ $previous_date }}">@icon( angle-left )</div>
                <div class="col-5 btn btn_icon font-weight-bold w-100">{{ $date_title }}</div>
                <div class="col btn btn_icon month_button" data-date="{{ $next_date }}"    >@icon( angle-right )</div>
                <script>
                    $('.month_button').on( 'click', function() {
                        var date = $(this).data('date');
                        $("#base_date").val( date );
                        $("#search_form").submit();
                    });
                </script>
            </div>
        </div>
        <a class="col-1 btn btn_icon ml-auto w-10" title="タスクリスト設定" href="{{ route( 'groupware.tasklist.index' ) }}">@icon( config )</a>
        <a class="col-1 btn btn_icon w-10" title="カレンダー設定"           href="{{ route( 'groupware.calendar.index' ) }}">@icon( config )</a>
    </div>
</div>