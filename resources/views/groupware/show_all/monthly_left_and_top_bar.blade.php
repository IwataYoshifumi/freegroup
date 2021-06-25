@php
use Illuminate\Support\Facades\Route;

$current_route         = Route::currentRouteName();


$calendars = ( is_array( $request->calendars )) ? $request->calendars : [];
$tasklists = ( is_array( $request->tasklists )) ? $request->tasklists : [];

@endphp


<div class="cal3 left_area" id="left_area">
    {{ Form::open( [ 'route' => $current_route, 'method' => 'GET', 'id' => 'search_form' ] ) }}
        @csrf
        {{ Form::hidden( 'base_date', $request->base_date, ['id' => 'base_date' ] ) }}

        <div class="container">
            <div class="row">
                <div class="col-12 d-flex sidebar_headar border border-dark" style="background-color: peachpuff">
                    <span class="btn btn_icon m-1 mr-auto" id="sidebar_closer">@icon( arrow-left ) </span>
                </div>
 
                {{--
                  --
                  -- カレンダー・タスクの検索条件
                  --
                  --
                  --}}
                <div class="col-12 shadow btn btn-light sidebar_headar left_menus m-1 font-weight-bold" data-target="config">検索条件</div>
                <div class="config shadow" style="width: 100%">
                    <div class="col-12 m-2">
                        @php
                        $permissions = [ 'owner' => 'カレンダー管理者', 'writer' => '予定追加可', 'reader' => '予定閲覧可' ];
                        @endphp

                        <label for="show_hidden_calendars">非表示カレンダーを表示</label>
                        {{ Form::checkbox( 'show_hidden_calendars', 1, $request->show_hidden_calendars, [ 'id' => 'show_hidden_calendars', 'class' => 'checkboxradio' ] ) }}
                        {{ Form::select( 'calendar_permission', $permissions, $request->calendar_permission, [ 'class' => 'form-control' ] ) }}
                    </div>
                    <div class="col-12 m-2">
                        @php
                        $permissions = [ 'owner' => 'タスクリスト管理者', 'writer' => 'タスク追加可', 'reader' => 'タスク閲覧可' ];
                        @endphp

                        <label for="show_hidden_tasklists">非表示タスクリストを表示</label>
                        {{ Form::checkbox( 'show_hidden_tasklists', 1, $request->show_hidden_tasklists, [ 'id' => 'show_hidden_tasklists', 'class' => 'checkboxradio' ] ) }}
                        {{ Form::select( 'tasklist_permission', $permissions, $request->tasklist_permission, [ 'class' => 'form-control' ] ) }}
                    </div>
                </div>
 
                {{--
                  --
                  -- カレンダー　表示フォーム
                  --
                  --
                  --}}
                <div class="col-12 shadow btn btn-light sidebar_headar left_menus m-1 font-weight-bold" data-target="calendars">カレンダー</div>
                <div class="calendars" style="width: 100%">
                    <div class="col-12 shadow border p-2">
                        <div class="btn btn-sm btn-outline-dark" id="toggle_schedules_btn" data-show='0'>予定を表示</div>
                        <script>
                            $("#toggle_schedules_btn").on( 'click', function() {
                                var show = $(this).data('show');
                                if( show == 1 ) {
                                    show = 0;
                                    $(this).html( '予定を表示' );                                    
                                } else {
                                    show = 1;
                                    $(this).html( '予定を非表示' );
                                }
                                $(this).data('show', show );
                                $('.calendar_checkboxes').each( function() {
                                    $(this).prop('checked', show );
                                });
                            });
                        </script>
                    </div>
                    @foreach( $returns['list_of_calendars'] as $i => $calendar )
                        @php
                            $calprop = $calendar->my_calprop();
                            $id = 'calendar_' . $calendar->id;
                            $h = 50 +  $sidebar_height * $i;
                            $style = $calprop->style();
                            $checked = ( in_array( $calendar->id, $calendars )) ? 1 : 0;
                            $url_to_calprop = route( 'groupware.calprop.show', [ 'calprop' => $calprop->id ] );
                        @endphp
                        <div class="col-12 sidebar_lists left_menus border border-light" style="{{ $style }}">
                            <div class="d-flexa">
                                {{ Form::checkbox( 'calendars[]', $calendar->id, $checked, [ 'id' => $id, 'class' => 'calendar_checkboxes' ] ) }}
                                <span class="checkbox-area flex-grow-1 " data-id="{{ $id }}">{{ $calprop->name }}</span>
                                <a class="btn btn-sm uitooltip" title="カレンダー「{{ $calprop->name }}」表示設定" href="{{ $url_to_calprop }}">@icon( config )</a>
                            </div>
                        </div>
                    @endforeach
                </div>


                {{--
                  --
                  -- タスクリスト　表示フォーム
                  --
                  --
                  --}}
                @php
                    $array_task_status = [ '完了' => '完了', '未完' => '未完のみ', '' => '完了・未完' ];                
                
                @endphp
                <div class="col-12 shadow btn btn btn-light sidebar_headar left_menus m-1 font-weight-bold" data-target="tasklists">タスクリスト</div>
                <div class="tasklists" style="width: 100%">
                    <div class="col-12 shadow border p-2">
                        <div class="btn btn-sm btn-outline-dark" id="toggle_tasks_btn" data-show='0'>タスクを表示</div>
                        <script>
                            $("#toggle_tasks_btn").on( 'click', function() {
                                var show = $(this).data('show');
                                if( show == 1 ) {
                                    show = 0;
                                    $(this).html( 'タスクを表示' );                                    
                                } else {
                                    show = 1;
                                    $(this).html( 'タスクを非表示' );
                                }
                                $(this).data('show', show );
                                $('.tasklists_checkboxes').each( function() {
                                    $(this).prop('checked', show );
                                });
                            });
                        </script>
                        {{ Form::select( 'task_status', $array_task_status, $request->task_status, [ 'class' => 'formcontrol' ] ) }}
                    </div>
                    
                    
                    @foreach( $returns['list_of_tasklists'] as $i => $tasklist )
                        @php
                            $taskprop = $tasklist->my_taskprop();
                            $id = 'tasklist_' . $tasklist->id;
                            $h = 50 +  $sidebar_height * $i;
                            $style = $taskprop->style();
                            $checked = ( in_array( $tasklist->id, $tasklists )) ? 1 : 0;
                            $url_to_taskprop = route( 'groupware.taskprop.show', [ 'taskprop' => $taskprop->id ] );
                        @endphp
                        <div class="col-12 sidebar_lists left_menus border border-light" style="{{ $style }}">
                            {{ Form::checkbox( 'tasklists[]', $tasklist->id, $checked, [ 'id' => $id, 'class' => 'tasklists_checkboxes' ] ) }}
                            <span class="checkbox-area" data-id="{{ $id }}">{{ $taskprop->name }}</span>
                            <a class="btn btn-sm uitooltip" title="タスクリスト「{{ $taskprop->name }}」表示設定" href="{{ $url_to_taskprop }}">@icon( config )</a>
                        </div>
                    @endforeach
                </div>
                <script>
                    $('.checkbox-area').on( 'click', function() {
                        var id = $(this).data('id');
                        console.log( id );
                        id = "#" + id;
                        console.log( id, $(id).prop('checked'), $(id).val() );
                        
                        
                        if( $(id).prop('checked') ) {
                            $(id).prop('checked', false );
                        } else {
                            $(id).prop('checked', true );
                        }
                    });              
                </script>
            </div>
        </div>
        <div class="col-12 shadow-lg p-2">
            <div class="btn btn-outline-dark shadow col-11" onClick="search_form_submit()">再表示</div>
        </div>
        <script>
            function search_form_submit() {
                $("#search_form").submit();
            }
        </script>
        
        
    {{ Form::close() }}
</div>

<div class="cal3 top_area border border-dark" style="background-color: peachpuff" id="top_area">
    <div class="row no-gutters">
        <div class="col-2 w-10 mr-auto">
            <div class="row">
                @php
                
                $route_create_schedule = route( 'groupware.schedule.create' );

                $route_create_task     = route( 'groupware.task.create' );

                $route_index_schedule  = route( 'groupware.show_all.index', [ 'writable_calender' => 1, 'set_defaults' => 1 ] );
                $route_index_task      = route( 'groupware.show_all.index', [ 'writable_tasklist' => 1, 'set_defaults' => 1 ] );
                @endphp
                
                <div class="col btn ml-2 btn_icon" id="sidebar_opener">@icon( angle-double-right )</div>
                <a class="col btn btn_icon text-primary uitooltip" title="新規スケジュール作成" href="{{ $route_create_schedule }}">@icon( plus-circle  )</a>
                <a class="col btn btn_icon text-primary uitooltip" title="新規タスク作成"       href="{{ $route_create_task     }}">@icon( check-circle )</a>
                
                <a class="col btn btn_icon uitooltip" title="スケジュール検索"  href="{{ $route_index_schedule }}">@icon( search )スケジュール検索</a>
                <a class="col btn btn_icon uitooltip" title="タスク検索"        href="{{ $route_index_task     }}">@icon( search )タスク検索</a>

                
            </div>
        </div>
        <div class="col-3">
            <div class="row">
                @php
                $previous_month = $base_date->copy()->subMonth()->format( 'Y-m-d' );
                $next_month     = $base_date->copy()->addMonth()->format( 'Y-m-d' ) 
                @endphp
                
                <div class="col btn btn_icon month_button" data-date="{{ $previous_month }}">@icon( angle-left )</div>
                <div class="col btn btn_icon font-weight-bold">{{ $base_date->format( 'Y年 m月' ) }}</div>
                <div class="col btn btn_icon month_button" data-date="{{ $next_month }}"    >@icon( angle-right )</div>
                <script>
                    $('.month_button').on( 'click', function() {
                        var date = $(this).data('date');
                        $("#base_date").val( date );
                        $("#search_form").submit();
                    });
                </script>
            </div>
        </div>
        @php 
        $url = route( 'groupware.calendar.index' );
        @endphp
        <a class="col-1 btn btn_icon ml-auto w-10" title="カレンダー設定" href="{{ $url }}">@icon( config )</a>
    </div>
</div>

<!--
<div class="font-weight-bold">
    <div id="window-size"></div>
    <div id="main-size"></div>
</div>
-->

<script>

    $('.sidebar_headar').on( 'click', function() {
        var target = $(this).data( 'target' );
        var t      = "." + target;
        var options = { percent: 50 };
        $( t ).toggle( 'blind', options , 200 );
        
    });
    
    $(document).ready( function() {
       $('.config').hide();
    });
    
    var breakpoint_sm = 576;
    var breakpoint_md = 768;
    var breakpoint_lg = 992;
    var left_area = $('#left_area');
    var sidebar_opener = $('#sidebar_opener');
    var sidebar_closer = $('#sidebar_closer');
    // var main_area = $('#main_area');
    var top_area  = $('#top_area');
    var head_area = $('#head_area');
    var left_menus = $('.left_menus');
    var left_offset = 250;
    var top_offset  = 150;

    var is_left_area_hidden = false;
    
    $(window).on( 'resize load', function( event ) {
        var w_width = $(window).width();
        var w_height = $(window).height();
        
        resize_main_area();
        console.log( event.type );
        
        if( w_width < breakpoint_md ) {
            hide_sidebar();
        } else if( w_width >= breakpoint_md ) {
            //show_sidebar();
            setTimeout( function() { sidebar_opener.show(); } , 500 );
        }
        top_area.scrollLeft( 0 );
        
    });
    
    sidebar_closer.on( 'click', function() { hide_sidebar(); });
    sidebar_opener.on( 'click', function() { show_sidebar(); });
    
    function hide_sidebar() {

        var main_area = $('#main_area');
        var head_area = $('#head_area');

        is_left_area_hidden = true;        
        var css = { width: "0px", left: "20px" };        
        left_menus.animate( css, 500 );
        
        
        var css = { left: "5px" };        
        main_area.animate( css, 500 );
        top_area.animate( css, 500 );
        head_area.animate( css, 500 );
        
        var width = main_area.width() + left_offset;
        top_area.css( 'width', width + 'px' );
        head_area.css( 'width', width + 'px' );
        main_area.css( 'width', width + 'px' );
        setTimeout( function() { sidebar_opener.show(); } , 500 );


    }

    function show_sidebar() {

        console.log( 'exec show_sidebar' );
        var main_area = $('#main_area');
        var head_area = $('#head_area');

        is_left_area_hidden = false;
        var css = { width: left_offset + 'px', left: "0px" };
        left_menus.animate( css, 500 ); 

        var css = { left: left_offset + 'px' };
        main_area.animate( css, 500 );
        top_area.animate(  css, 500 );
        head_area.animate(  css, 500 );
        setTimeout( function() { resize_main_area(); }, 500 );
        sidebar_opener.hide();

    }

    function resize_main_area() {
        
        var main_area = $('#main_area');
        var head_area = $('#head_area');
        
        
        var num_of_weeks = {{ $num_of_weeks }};
    
        var w_width = $(window).width();
        var w_height = $(window).height();

        //var w_width = $(window.document).width();
        //var w_height = $(window.document).height();


        var main_height = w_height - top_offset;
        if( is_left_area_hidden == true ) {
            var main_width = w_width - 20; 
        } else {
            var main_width = w_width - left_offset - 10;
        }
        
        top_area.css(  'width', main_width );
        head_area.css( 'width', main_width );
        main_area.css( 'width', main_width );
        main_area.css( 'height', main_height );


        var w = "window 幅：" + w_width + " 高：" + w_height + "&nbsp; &nbsp;" ;
        var t = "main    幅：" + main_area.width() + " / 高：" + main_area.height() + "(" + main_height + ")";
        $('#window-size').html( w + t );
    }
    
</script>
