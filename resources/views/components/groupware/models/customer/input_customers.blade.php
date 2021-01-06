@php
    use App\myHttp\GroupWare\Models\Customer;

@endphp


<div class="clearfix">
    <!-- The only way to do great work is to love what you do. - Steve Jobs -->
    <div class="row">
        <div id="customer_ids_form" class="font-weight-bold col-12">
            @php
                #dump( old( 'customers' ), $customers );
                
                #if( is_array( $customers )) {
                #    $customers = Customer::find( $customers );
                #} 

            @endphp
            @foreach( $customers as $c ) 
                <div class='col customer_id' id='customer_id_{{ $c->id }}' data-customer_id='{{ $c->id }}'>
                    <div class='btn btn-sm btn-outline-secondary' onClick='delete_customer_id( {{ $c->id }} )'>-</div>
                    <input type=hidden name='customers[]' value={{ $c->id }}>{{ $c->name }}
                </div>
            @endforeach
        </div>
        <div class="col-12 m-1"></div>
        
        <div class="col-7 btn-group">
            {{ Form::text( 'customer_name', old( 'customer_name' ), [ 'class' => 'form-control', 'id' => 'search_customers', 'placeholder' => '名前・カナ' ] ) }}
        </div>
        <div class="btn btn-sm btn-outline-secondary col-1" onClick='clear_search_customers()'>x</div>
        <div id="customer_lists" class="bg-light">
            <div class="col schedule">1</div>
        </div>
    </div>
</div>

@once
<script language='JavaScript'>
    //　検索クリアーボタン
    //
    function clear_search_customers() {
        $('#search_customers').val( null );
        $('#search_customers').change();
    }

    //　顧客ID追加
    //
    function customer_id_click( id, name ) {
        // console.log( 'aa', id );
        try {
            $('.customer_id').each( function() {
                console.log( $(this).data('customer_id') ); 
                if( id === $(this).data('customer_id') ) {
                    // console.log( 'duplicate');
                    throw new Error('duplicate id');
                }
            });
            
            var form = $('#customer_ids_form');
            var tag = "<div class='col customer_id' id='customer_id_" + id + "' data-customer_id=" + id +">";
            tag    += "     <div class='btn btn-sm btn-outline-secondary'";
            tag    += "          onClick='delete_customer_id(" + id + ")'>-</div>";
            tag    += "     <input type=hidden name='customers[]' value=" + id +">"+ name;
            tag    += "</div>";
            form.append( tag );
            // console.log( id );
        } catch( e ) {
            console.log( 'customer_id_click duplicate ID');
        }
    };
    //　顧客ID削除ボタン
    //
    function delete_customer_id( id ) {
        console.log( id  );
        var elm = '#customer_id_' + id;
        console.log( $( elm ) );
        $( elm ).remove();
    };
    
    
    // $('.custmoer_ids').click( function() {
    //     console.log( $(this) ); 
    // });

    // 顧客検索フォーム
    //
    $('#search_customers').change( function() {
        var search = $(this).val();
        var url    = "{{ route( 'customer.json.search' ) }}";
        console.log( search );

        if( search ) { 
            console.log( 'NOT NULL');
            $.ajax( url, {
                ttype: 'get',
                data:  { name : search },
                dataType: 'json',
            }).done( function( data ) {
                console.log( data );
                $("#customer_lists").children().remove();
                $.each( data, function( i, val ) {
                    // var tag = "<div class='btn btn-sm btn-outline-secondary'>+</div>";
                    var tag = "<div class='col schedule customer_ids'";
                    tag += "         id=customer_id" + val.id;
                    tag += "         data-customer_id=" + val.id;
                    tag += "         value=" + val.id;
                    tag += "        >";
                    tag += "    <div class='btn btn-sm btn-outline-secondary'";
                    tag += "         onClick='customer_id_click(" + val.id + ",\"" + val.name + "\")'";
                    tag += "    >+</div>";
                    tag += "    【"+ val.id + "】" + val.name + "：" + val.address +"【" + val.age + "才】";
                    tag += "</div>";
                    // console.log( tag, name );
                    $("#customer_lists").append( tag ); 
                });
            });   
        } else {
            console.log( 'NULL' );
            $("#customer_lists").children().remove();
        }
    });
        
    $(document).ready( function() {
        $('#search_customers').change();
        
    });
        
</script>
@endonce