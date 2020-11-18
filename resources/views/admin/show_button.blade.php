<div class="m-1 w-100 container">
    <a class="btn btn-warning col-2 m-1" href="{{ route( 'admin.edit' , [ 'admin' => $admin->id ] ) }}">変更</a>
    <a class="btn btn-danger  col-2 m-1" href="{{ route( 'admin.index' ,[ 'admin' => $admin->id ] ) }}">削除</a>
</div>
