<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="@auth {{ route( 'home' ) }} @endauth @guest {{ route( 'home' ) }} @endguest">
            @if( is_debug() ) icon( debug ) @endif
            {{ config('app.name', 'myApp') }} No Auth
        </a>

        @auth
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">
                    
                    @if( Route::has( 'mail_order.create' ))
                        <a class="nav-item nav-link" href="{{ route( 'mail_order.create' ) }}">メールオーダー</a>
                    @endif
                    @if( Route::has( 'text.myform.input' )) 
                        <a class="nav-item nav-link" href="{{ route( 'test.myform.input' ) }}">テスト1</a>
                    @endif
                    @if( Route::has( 'sansan.form' )) 
                        <a class="nav-item nav-link" href="{{ route( 'sansan.form' )       }}">Sansan API</a>
                    @endif
                    <a class="nav-item nav-link" href="">menu3</a>

                    @if( 1 ) 
                        <div class="dropdown">
                            <a id="dropdownMenuButton"
                                    class="nav-item nav-link dropdown-toggle"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
	                             aria-expanded="false">【メニュー】</a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="z-index:9999;">
                                <a class="dropdown-item" href="">menu1</a>
                                <a class="dropdown-item" href="">menu2</a>
	                            <a class="dropdown-item" href="">menu3</a>
                            </div>
                        </div>
                        <div class="dropdown">
                            <a class="nav-item nav-link" href="">【メニュー】</a>
                        </div>
                        <div class="dropdown">
                            <a class="nav-item nav-link" href="">【メニュー】</a>
                        </div>
                    @endif
                </ul>

            @endauth

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <div class="top-right links">
                        @if (Route::has('user.home'))
                            @auth
                                <a href="{{ route( 'user.home' ) }}">User Home</a>
                            @else
                                @if( Route::has( 'user.login' ))
                                <a href="{{ route('user.login') }}">User Login</a>
                                @endif
                                @if ( Route::has('user.register'))
                                    <a href="{{ route('user.register') }}">User Register</a>
                                @endif
                            @endauth
                            <div class="col-12"></div>
                        @endif

                        @if (Route::has('admin.login'))
                            @auth
                                <a href="{{ url('/admin/home') }}">Admin Home</a>
                            @else
                                <a href="{{ route('admin.login') }}">Admin Login</a>

                                @if (Route::has('admin.register'))
                                    <a href="{{ route('admin.register') }}">Admin Register</a>
                                @endif
                            @endauth
                        @endif
                        
                        @if (Route::has('customer.login'))
                            @auth
                                <a href="{{ url('/customer/home') }}">Customer Home</a>
                            @else
                                <a href="{{ route('customer.login') }}">Customer Login</a>

                                @if (Route::has('customer.register'))
                                    <a href="{{ route('customer.register') }}">Customer Register</a>
                                @endif
                            @endauth
                        @endif
                    </div>

                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <span class="caret">{{ Auth::user()->name }}</span>
                        </a>
                    
                        @auth( 'user' )
                            @php $logout = 'user.logout' @endphp
                        @endauth
                        @auth( 'admin' )
                            @php $logout = 'admin.logout' @endphp
                        @endauth
                        @auth( 'customer' )
                            @php $logout = 'customer.logout' @endphp
                        @endauth
                        
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="">パスワード変更</a>
                            <a class="dropdown-item" href="{{ route( $logout ) }}"
                               onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                ログアウト</a>
                            <form id="logout-form" action="{{ route( $logout ) }}" method="POST" style="display: none;">
                            @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>