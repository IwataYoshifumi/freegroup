<div class="m-1 w-100 container">
    @if( auth( 'admin' )->check() )
        <a class="btn btn-warning col-3 m-1" href="{{ route( 'user.edit',    [ 'user' => $user->id ] ) }}">変更</a> 
        <a class="btn btn-danger  col-3 m-1" href="{{ route( 'user.delete' , [ 'user' => $user->id ] ) }}">削除</a>
    @endif
</div>