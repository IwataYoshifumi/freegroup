@php

use App\User;
use App\Http\Helpers\MyForm;

$labels = config( 'mail_order.columns_name' );

@endphp

下記の通り、注文をウェブフォームから受付しました。

<div>
注文日時 {{ now() }}
</div>

<h4>お客様情報</h4>
<table border>
        @foreach( config( 'mail_order.columns' ) as $column )
            <tr>
            <th class="col-4">{{ $labels[$column] }}</th>
            <td class="col-7">{{ Arr::get( $order, "input.$column" ) }}</td>
            </tr>
        @endforeach
</table>

<h4>納品先</h4>
<table border class="table table-strip">
        @foreach( config( 'mail_order.columns_delivery' ) as $column )
            <tr>
            <th class="col-4">{{ $labels[$column] }}</th>
            <td class="col-7">{{ Arr::get( $order, "input.$column" ) }}</td>
            </tr>
        @endforeach
</table>

<h4>注文内容</h4>
<table border class="table table-strip">
        <tr>
            <th>品名</th>
            <th>数量</th>
            <th>単価</th>
            <th>小計</th>
        </tr>
        @foreach( Arr::get( $order, 'item' ) as $i => $item )
            @if( ! empty( Arr::get( $order, "num.$i" ))) 
            <tr>
                <th class="col-4">{{ $item }}</th>
                <td class="col-2">{{ Arr::get( $order, "num.$i"      ) }}</td>
                <td class="col-2">{{ Arr::get( $order, "price.$i"    ) }}円</td>
                <td class="col-3">{{ Arr::get( $order, "subtotal.$i" ) }}円</td>
            </tr>
            @endif
        @endforeach
            <tr>
                <th>小計</th>
                <td colspan=3>{{ Arr::get( $order, 'all_subtotal' ) }}円</td>
            </tr>
            <tr>
                <th>消費税</th>
                <td colspan=3>{{ Arr::get( $order, 'tax' ) }}円</td>
            </tr>
            <tr>
                <th>合計</th>
                <td colspan=3>{{ Arr::get( $order, 'total' ) }}円</td>
            </tr>



</table>





