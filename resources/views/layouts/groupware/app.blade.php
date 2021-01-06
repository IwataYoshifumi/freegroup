<body>
    <div id="app">
        @auth( 'user' )
            @include( 'layouts.groupware.menu_user' )
        @endauth
        
        @auth( 'admin' )
            @include( 'layouts.groupware.menu_admin' )
        @endauth

        @guest( 'user' ) @guest( 'admin' )
            @include( 'layouts.groupware.menu_guest' )
        @endguest @endguest

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>