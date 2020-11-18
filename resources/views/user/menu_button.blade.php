<div class="m-1 w-100 container">
    @if( auth( 'admin' )->check() )
        <a class="btn btn-primary col-3 m-1" href="{{ route( 'user.create', [ 'root_route' => 1 ] ) }}">新規　社員登録</a> 
    @endif
    <a class="btn btn-outline-secondary col-3 m-1" href="{{ route( 'user.index' , [ 'root_route' => 1 ] ) }}">社員一覧</a>

    @if( 0 )
        <!-- 開発中　-->
        <a class="btn btn-outline-secondary col-2 m-1" href="{{ route( 'user.select' , [ 'root_route' => 1 ] ) }}">選択</a>
    @endif
</div>
