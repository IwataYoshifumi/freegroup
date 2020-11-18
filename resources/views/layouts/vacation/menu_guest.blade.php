<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="{{ route( 'welcome' ) }}">
            {{ config('app.name', 'Laravel') }}
        </a>

        @if( 1 )
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto">

            <ul class="navbar-nav ml-auto">
                @guest( 'user', 'admin' )
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('user.login') }}">社員ログイン</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.login') }}">管理者ログイン</a>
                    </li>
                @endguest
            </ul>
        @endif
        </div>
    </div>
</nav>