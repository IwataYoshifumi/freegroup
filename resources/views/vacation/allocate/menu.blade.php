<div class="m-2">
    @auth( 'admin' )
        <a class="btn btn-success col-3 m-1" href="{{ route( 'vacation.allocate.select', [ 'root_route' => 1 ] ) }}">有給割当</a>
        <a class="btn btn-success col-3 m-1" href="{{ route( 'vacation.vacation.index',  [ 'root_route' => 1 ] ) }}">有給割当【一覧】</a>
    @endauth
</div>