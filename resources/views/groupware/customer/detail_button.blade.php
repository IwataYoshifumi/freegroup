<div class="row">
    <a class="btn btn_icon col-1 ml-auto" href="{{ route( 'customer.edit' ,   [ 'customer' => $customer->id ] ) }}" title="変更">@icon( edit )</a>
    <a class="btn btn_icon col-1 m-1" href="{{ route( 'customer.delete' , [ 'customer' => $customer->id ] ) }}" title="削除">@icon( trash )</a>
</div>
