@php
$num_of_weeks = 2;

@endphp

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
