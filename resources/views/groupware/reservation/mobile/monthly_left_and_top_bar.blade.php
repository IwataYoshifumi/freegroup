@php
use Illuminate\Support\Facades\Route;

$current_route         = Route::currentRouteName();


$facilities = ( is_array( $request->facilities )) ? $request->facilities : [];

@endphp


<div class="left_area border border-light shadow " id="left_area">
    {{ Form::open( [ 'route' => $current_route, 'method' => 'GET', 'id' => 'search_form' ] ) }}
        @csrf
        {{ Form::hidden( 'base_date', $request->base_date, ['id' => 'base_date' ] ) }}

        <div class="container">
            <div class="row">
                <div class="col-12 d-flex sidebar_headar border border-dark" style="background-color: palegreen">
                    <!--<span class="btn btn_icon m-1 mr-auto" id="sidebar_closer">@icon( arrow-left ) </span>-->
                </div>
 
 
                <div class="col-12 shadow-lg p-2">
                    <div class="btn btn-outline-dark btn-light shadow col-11" onClick="search_form_submit()">再表示</div>
                </div>
                {{--
                  --
                  -- 設備リスト
                  --
                  --
                  --}}
                <div class="col-12 shadow btn btn-light sidebar_headar left_menus m-1 font-weight-bold" data-target="facilities">設備</div>
                <div class="facilities" style="width: 100%">
                    <div class="col-12 shadow border p-2">
                        <x-facility_checkboxes :facilities="op( $request )->facilities" name="facilities" button="設備検索" />
                    </div>      
                </div>

                {{--
                  --
                  -- 社員・部署検索
                  --
                  --
                  --}}
                <div class="col-12 shadow btn btn btn-light sidebar_headar left_menus m-1 font-weight-bold" data-target="users">社員・部署</div>
                <div class="users" style="width: 100%">
                    <div class="col-12 shadow border m-2 p-1">
                        <x-checkboxes_users :users="op( $request )->users" button="社員" />
                        <hr>
                        <x-checkboxes_depts :depts="op( $request )->depts" name="depts" button="部署" />
                    </div>
                </div>
                
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

<div class="top_area border border-dark" style="background-color: palegreen" id="top_area">
    <div class="row no-gutters d-flex junstify-content-center bg-inf">
        <div class="flex-fill">
            {{--
              --
              --  新規ボタン・検索ボタン
              --
              --}}
            
            <!--<div class="btn ml-2 btn_icon" id="sidebar_opener">@icon( angle-double-right )</div>-->
        </div>
        <div class="">
            @php
            $previous_date = $base_date->copy()->subMonth()->format( 'Y-m-d' );
            $next_date     = $base_date->copy()->addMonth()->format( 'Y-m-d' );
            $date_title    = $base_date->format( 'Y年 n月' );
            $today         = today()->format('Y-m-d');
            @endphp
            {{--
              --
              --
              --　月切替ボタン
              --
              --}}
            <div class="btn btn_icon month_button" data-date="{{ $previous_date }}">@icon( angle-left )</div>
            <div class="btn btn_icon font-weight-bold">{{ $date_title }}</div>
            <div class="btn btn_icon month_button" data-date="{{ $next_date }}"    >@icon( angle-right )</div>
            <div class="btn btn-sm border border-dark month_button" date-date="{{ $today }}">今月</div>
            <script>
                $('.month_button').on( 'click', function() {
                    var date = $(this).data('date');
                    $("#base_date").val( date );
                    $("#search_form").submit();
                });
            </script>
        </div>
        <div class="flex-fill"></div>
        
    </div>
</div>

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
    
    $(window).on( 'load', function() {
        console.log( 'load' );
        @if(( ! is_array( $request->facilities ) or count( $request->facilities ) == 0 )) 
            $(".facilities").toggle();
        @endif
        
        @if(( ! is_array( $request->tasklists ) or count( $request->tasklists ) == 0 )) 
            $(".tasklists").toggle();
        @endif
        
        @if(( ! is_array( $request->users ) or count( $request->users ) == 0 ) and ( ! is_array( $request->depts ) or count( $request->depts ) == 0 )) 
            $(".users").toggle();
        @endif
        
        @if( ! is_array( $request->customers ) or count( $request->customers ) == 0 ) 
            $(".customers").toggle();
        @endif
    });
    
    sidebar_closer.on( 'click', function() { hide_sidebar(); });
    sidebar_opener.on( 'click', function() { show_sidebar(); });
    
    function hide_sidebar() {

        var main_area = $('#main_area');
        var head_area = $('#head_area');

        is_left_area_hidden = true;        
        // var css = { width: "0px", left: "20px" };        
        // left_menus.animate( css, 500 );
        left_area.hide( 'blind', 500 );
        
        var css = { left: "5px" };        
        main_area.animate( css, 500 );
        top_area.animate( css, 500 );
        head_area.animate( css, 500 );
        
        var width = main_area.width() + left_offset;
        top_area.css( 'width', width + 'px' );
        head_area.css( 'width', width + 'px' );
        main_area.css( 'width', width + 'px' );
        
        setTimeout( function() { sidebar_opener.show(); } , 200 );
    }

    function show_sidebar() {

        console.log( 'exec show_sidebar' );
        var main_area = $('#main_area');
        var head_area = $('#head_area');

        is_left_area_hidden = false;
        // var css = { width: left_offset + 'px', left: "0px" };
        // left_menus.animate( css, 500 );


        var css = { left: left_offset + 'px' };
        main_area.animate( css, 500 );
        top_area.animate(  css, 500 );
        head_area.animate(  css, 500 );
        setTimeout( function() { resize_main_area(); }, 500 );
        sidebar_opener.hide();
        setTimeout( function() { left_area.show( 'blind', 50 ); } , 500 );

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
