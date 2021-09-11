<body>
    <div id="app">
        <div id="top_navi_bar">
            @auth( 'user' )
                @include( 'layouts.groupware.menu_user' )
            @endauth
            
            @auth( 'admin' )
                @include( 'layouts.groupware.menu_admin' )
            @endauth
    
            @guest( 'user' ) @guest( 'admin' )
                @include( 'layouts.groupware.menu_guest' )
            @endguest @endguest
        </div>

        <main id="main_body">
            @yield('content')
        </main>
        
        <script>
            /*
             *
             * 画面サイズの設定
             *
             */
            @php
            $screen_size = session( 'ScreenSize' );
            @endphp
        
            var window_width  = {{ op( $screen_size )['width']  }};
            var window_height = {{ op( $screen_size )['height'] }};
            var top_navi_bar_height = $('#top_navi_bar').height();
            
            $( function() {

               var main_body = $('#main_body');

                console.log( 'top_navi_bar_height', top_navi_bar_height, window_height, window_width, window_height - top_navi_bar_height );

               
               main_body.width(  window_width );
               main_body.height( window_height - top_navi_bar_height );
            });
        </script>
        
        
    </div>
</body>