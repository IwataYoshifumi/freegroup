<div class="row">
    <a class="btn btn-warning col-2 m-1" href="{{ route( 'customer.edit' , [ 'customer' => $customer->id  ] ) }}">変更</a>
    <a class="btn btn-danger  col-2 m-1" href="{{ route( 'customer.delete' , [ 'customer' => $customer->id ] ) }}">削除</a>
</div>
