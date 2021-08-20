<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="icon" type="image/png" href="/freegroup.png">

        <title>FreeGroup</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="top-right links">
                @if (Route::has('login'))
                    @auth
                        @if( Route::has( 'home' )) 
                            <a href="{{ route('home') }}">Home</a>
                        @endif
                    @else
                        @if( Route::has( 'login' )) 
                            <a href="{{ route('login') }}">Login</a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                    <div class="col-12"></div>
                @endif
            
                @if (Route::has('user.login'))
                    @auth( 'user' )
                        <a href="{{ url('/user/home') }}">User Home</a>
                    @else
                        <a href="{{ route('user.login') }}">User Login</a>

                        @if (Route::has('user.register'))
                            <a href="{{ route('user.register') }}">User Register</a>
                        @endif
                    @endauth
                    <div class="col-12"></div>
                @endif

                @if (Route::has('admin.login'))
                    @auth( 'admin' )
                        <a href="{{ url('/admin/home') }}">Admin Home</a>
                    @else
                        <a href="{{ route('admin.login') }}">Admin Login</a>

                        @if (Route::has('admin.register'))
                            <a href="{{ route('admin.register') }}">Admin Register</a>
                        @endif
                    @endauth
                    <div class="col-12"></div>
                @endif
                
                @if (Route::has('customer.login') and 0 )
                    @auth( 'customer' )
                        <a href="{{ url('/customer/home') }}">Customer Home</a>
                    @else
                        <a href="{{ route('customer.login') }}">Customer Login</a>

                        @if (Route::has('customer.register'))
                            <a href="{{ route('customer.register') }}">Customer Register</a>
                        @endif
                    @endauth
                @endif
            </div>

            <div class="content">

                <div class="links"><a href="">〇〇〇株式会社</a></div>

                <h1 class="m-b-md">
                        <a href="https://freegroup.work" style="text-decoration: none; color: #636bbf;">グループウエア FreeGroup</a>
                </h1>

                <div class="bottom-right links">
                	<a href="https://www.network-tokai.jp">開発元：株式会社ネットワーク東海</a>
                </div>
                	    
            </div>
        </div>
    </body>
</html>
