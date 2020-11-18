<body>
    <div id="app">
        @auth( 'user' )
            <!--
                include( 'layouts.menu_user' )
            -->
            @include( 'layouts.vacation.menu_user' )
        @endauth
        @auth( 'admin' )
            <!--
                include( 'layouts.menu_admin' )
            -->
            @include( 'layouts.vacation.menu_admin' )
        @endauth
        @guest( 'user' ) @guest( 'admin' )
            <!--
                include( 'layouts.menu_guest' )
            -->
            @include( 'layouts.vacation.menu_guest' )
        @endguest @endguest
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>