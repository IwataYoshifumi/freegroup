<div class="card">
    <div class="card-header">注文内容</div>
                
    <div class="card-body m-1 border border-dark">

        <div class="d-none d-md-block">
            <div class="row">
                <div class="col-md-5">品名</div>
                <div class="col-md-2 text-center">注文数量</div>
                <div class="col-md-2 text-center">単価</div>
                <div class="col-md-2 text-center">小計</div>
            </div>
        </div>
                    
        @foreach( config( 'mail_order.items' ) as $i => $row )
            <div class="row">
                <div class="col-12 col-md-5">{{ $row['name'] }}</div>
                {{ Form::hidden( "item[$i]", $row['name'] ) }}
                {{ Form::hidden( "price[$i]", $row['price'] ) }}
                {{ Form::number( "num[$i]"     , old( "num.$i" ),    [ 'id' => "num_$i", 'data-id' => $i, 'class' => 'num text-center col-2' ] ) }}

                <div id="price_{{$i}}" data-price="{{ $row['price'] }}" class="col-2 text-right">{{ $row['price'] }}円</div>            

                {{ Form::number( "subtotal[$i]", old( "subtotal.$i" ),
                                    [ 'id' => "subtotal_$i", 'class' => 'subtotal text-right col-2', 'readonly' ] ) }}
                <div class="col-12"><hr class="w-95 d-md-none"></div>
            </div>
        @endforeach
                    
        <div class="row">
            <div class="col-12"><hr class="w-95 d-none d-md-block"></div>
            <div class="col-12 col-md-7">合計</div>
            {{ Form::number( "all_subtotal", old( "all_subtotal",0 ), [ 'id' => 'subtotal', 'class' => 'col-4 text-right', 'readonly' ] ) }}
            <div class="col-12 col-md-7">消費税</div>
            {{ Form::number( "tax", old( "tax", 0 ), [ 'id' => 'tax', 'class' => 'col-4 text-right', 'readonly' ] ) }}
            <div class="col-12 col-md-7">税込合計金額</div>
            {{ Form::number( "total", old( "total", 0 ), [ 'id' => 'total', 'class' => 'col-4 text-right', 'readonly' ] ) }}
        </div>
    </div>
    <script type="text/javascript">
        jQuery(function() {

            $(".num").change( function() {
                var i     = $(this).data('id');
                var num   = $(this).val();
                var price = $('#price_'+i).data('price');
                var subtotal = num * price;
                $('#subtotal_'+i).val( subtotal );
    
                var subtotal = 0;
                for( i=0; i<=$(".num").length-1; i++) {
                    //console.log($("#subtotal_"+i).val() );
                    if( $("#subtotal_"+i).val() !== "" ) {
                        subtotal = parseInt( subtotal ) + parseInt( $("#subtotal_"+i).val() );
                                    
                    }
                }
                var tax = Math.floor( subtotal * 0.1 );
                var total = subtotal + tax;
                console.log( tax, total );
                $("#subtotal").val( subtotal );
                $("#tax").val( tax );
                $("#total").val( total );
            });
        });
    </script>
</div>