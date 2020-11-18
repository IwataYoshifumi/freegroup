@php
use App\Http\Controllers\Vacation\VacationMenu;
#use App\MyApps\Vacation\VacationMenu;
use App\myHttp\Schedule\Menu as ScheduleMenu;


@endphp

<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="@auth( 'user' ) {{ route( 'user.home' ) }} @endauth @guest( 'user' ) {{ route( 'user.home' ) }} @endguest">
            {{ config('app.name', 'Laravel') }}
        </a>

        @if( 1 )
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

                    @auth( 'user' )
                        <!--
                        // 有給休暇申請アプリ用ユーザ（社員）メニュー
                        //
                        <!--
                        {{ VacationMenu::user_menus() }} 
                        -->
                        <!--
                        // 予定・日報システム　社員用メニュー
                        //
                        <!-- -->
                        ScheduleMenu::user_menus()
                        <!-- -->
                    @endauth

                    @if( 0 ) 
                        <div class="dropdown">
                            <a id="dropdownMenuButton"
                                    class="nav-item nav-link dropdown-toggle"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
	                             aria-expanded="false">【メニュー1】</a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" style="z-index:9999;">
                                <a class="dropdown-item" href="">menu</a>
                                <a class="dropdown-item" href="">menu</a>
	                            <a class="dropdown-item" href="">menu</a>
                            </div>
                        </div>

                        <div class="dropdown">
                            <a id="dropdownMenuButton_2"
                                    class="nav-item nav-link dropdown-toggle"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
	                             aria-expanded="false">【メニュー2】</a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton_2" style="z-index:9999;">
                                <a class="dropdown-item" href="">menu</a>
                                <a class="dropdown-item" href="">menu</a>
	                            <a class="dropdown-item" href="">menu</a>
                            </div>
                        </div>

                        
                        <div class="dropdown">
                            <a id="dropdownMenuButton_3"
                                    class="nav-item nav-link dropdown-toggle"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
	                             aria-expanded="false">【個別アプリ】</a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton_3" style="z-index:9999;">
                                <a class="dropdown-item" href="{{ route( 'mail_order.create' ) }}">メールオーダー</a>
                                <a class="dropdown-item" href="{{ route( 'test.myform.input' ) }}">入力フォーム</a>
	                            <a class="dropdown-item" href="{{ route( 'sansan.form' )       }}">Sansan</a>
                            </div>
                        </div>
                    @endif
                </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest( 'user' )
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('user.login') }}">Userログイン</a>
                    </li>
                    @if ( Route::has('user.register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('user.register') }}">User登録</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            <span class="caret">{{ optional( auth( 'user' )->user())->name }}</span>
                        </a>
                    
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route( 'user.change_password' ) }}">パスワード変更</a>
                            <a class="dropdown-item" href="{{ route('user.logout') }}"
                               onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                ログアウト</a>
                            <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
                            @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        @endif
        </div>
    </div>
</nav>