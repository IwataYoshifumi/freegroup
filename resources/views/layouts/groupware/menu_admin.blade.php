@php
use App\Http\Controllers\Vacation\VacationMenu;
#use  App\MyApps\Vacation\VacationMenu;
#use App\myHttp\Schedule\Menu as ScheduleMenu;
use App\myHttp\GroupWare\Menu as GroupwareMenu;

@endphp

<nav class="navbar navbar-expand-md navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="@auth('admin') {{ route( 'admin.home' ) }} @endauth @guest {{ route( 'admin.home' ) }} @endguest">
        @if( is_debug() ) @icon( debug ) @endif  {{ config('app.name', 'myApp') }} 

        </a>

        @auth( 'admin' )
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">
                    
                    <!--
                    // 予定・日報システム　管理者メニュー
                    //
                    -->
                    @include( 'layouts.groupware.include_menu_for_admin' )
                    {{-- 
                      --
                      -- 開発用メニュー
                      --
                      --}}
                    @include( 'layouts.groupware.include_menu_for_dev' )
                    
                    @if( 0 ) {
                        @if( Route::has( 'user.index' ))
                            <a class="nav-item nav-link" href="{{ route( 'groupware.user.index' ) }}">ユーザ管理</a>
                        @endif
                        @if( Route::has( 'admin.index' ))
                            <a class="nav-item nav-link" href="{{ route( 'admin.index' ) }}">管理者管理</a>
                        @endif
                        
                        
                        <a class="nav-item nav-link" href="">menu</a>
                        <a class="nav-item nav-link" href="">menu</a>
                    @endif

                    @if( 0 ) 
                        <div class="dropdown">
                            <a id="dropdownMenuButton"
                                    class="nav-item nav-link dropdown-toggle"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
	                             aria-expanded="false">【管理業務】</a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="z-index:9999;">
                                <a class="dropdown-item" href="">部署管理</a>
                                <a class="dropdown-item" href="">ユーザ管理</a>
	                            <a class="dropdown-item" href="">管理者管理</a>
                            </div>
                        </div>
                    
                        <div class="dropdown">
                            <a class="nav-item nav-link" href="">メニュー１</a>
                        </div>
                        <div class="dropdown">
                            <a class="nav-item nav-link" href="">メニュー２</a>
                        </div>
                    
                    @endif
                </ul>

            @endauth

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest( 'admin' )
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.login') }}">管理者ログイン</a>
                    </li>
                    @if ( Route::has('admin.register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.register') }}">管理者登録</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <span class="caret">{{ optional( auth('admin')->user())->name }}</span>
                        </a>
                    
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('admin.change_password') }}">パスワード変更</a>
                            <a class="dropdown-item" href="{{ route('admin.logout') }}"
                               onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                ログアウト</a>
                            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                            @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>