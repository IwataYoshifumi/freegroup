@php
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Customer;

$form_name = $name . "[]";
$button_id = $name . "_component_checkboxes_customers_opener";
$dialog_id = $name . "_component_checkboxes_customers_dialog";
$input_class = $name . "_component_checkboxes_class";
$customer_name_id = $dialog_id . "_customer_name";

$prepend_id = $name . "_component_checkboxes_prepend";
$hidden_id  = $name . "_component_hidden_prepend";


$url   = route( 'ajax.customer.search' );
#if_debug( $url, $form_name, $button_id, $dialog_id );


$customer_ids = ( count( $customers )) ? $customers->pluck('id')->toArray() : [];
//dump( 'pluck', $customers, old( 'customers' ), is_array( $customers ), count( $customers ), count( $customers ) >= 1 );
//$customer_ids = ( is_array( $customers ) and count( $customers ) >= 1 ) ? $customers->pluck('id')->toArray() : [];
$customer_ids = implode( ",", $customer_ids );

@endphp

<div class="m-1">
    <div id="{{ $hidden_id }}" class="row">
        @foreach( $customers as $customer )        
            <input type='hidden' name='{{ $form_name }}' value='{{ $customer->id }}'>
            <div class='{{ $form_class }} m-1'>{{ $customer->name }}</div>
        @endforeach
    </div>
</div>

<div class="btn btn-outline-secondary" id='{{ $button_id }}'>{{ $button }}</div>

<div id='{{ $dialog_id }}' title="{{ $button }}">

    <div class="row">
        {{ Form::text( 'customer_name', old( 'customer_name' ), [ 'class' => 'form-control col-6 m-1', 'id' => $customer_name_id, 'autocomplete' => 'off', 'placeholder' => '顧客名検索'  ] ) }}
        <button type=button class="btn btn-outline-dark btn-sm col-2 m-1" onClick="{{ $name }}_search_customers()">検索</button>
    </div>
    <hr>

    <div class="row">
        @foreach( $customers as $customer )
            @php
                $form_id = $dialog_id ."_" . $customer->id;
                $form_group_id = $form_id . "_group";
            @endphp
            <div id="{{ $form_group_id }}" class="col-3">
                <label for="{{ $form_id }}" class"w-100">{{ $customer->name }}</label>
                <input type="checkbox", name="{{ $form_name }}" value="{{ $customer->id }}" checked class="checkboxradio {{ $input_class }}" id="{{ $form_id }}" data-customer_name="{{ $customer->name }}">
                {{-- Form::checkbox( $form_name, $customer->id, true, [ 'class' => 'checkboxradio', 'id' => $form_id ] ) --}}
            </div>
        @endforeach
        <div id="{{ $prepend_id }}"></div>

    </div>
    

</div>

<script>
    // $('#{{ $customer_name_id }}').on( 'change', function() { search_customers(); });

    /*
     *
     *　顧客を検索
     *
     */
    function {{ $name }}_search_customers() {
        var url = '{{ $url }}';
        var customer_name = $('#{{ $customer_name_id }}').val();
        var token = '{{ csrf_token() }}';
        console.log( url, customer_name );

        $.ajax(
            url, {
            ttype: 'GET',
            data: { customer_name: customer_name } 
        }).done( function( data, status, xhr ) {
            console.log( data, status,xhr  );
            {{ $name }}_prepend_checkboxes( data );

        }).fail( function( xhr, status, error ) {
            console.log( status, error, xhr );
            alert( 'エラーで削除できませんでした');
        }); 
    }


    var customer_ids = [ {{ $customer_ids }} ]; 
    /*
     *
     *　検索結果から顧客選択チェックボックスを生成
     *
     */
    function {{ $name }}_prepend_checkboxes( data ) {

        if( data.length == 0 ) { alert( 'この条件では何も検索できませんでした' ); return; }
        
        for( var id in data ) {
            
            var in_array = customer_ids.some( function( value ) { return ( value == id ) } ); 
            if( in_array ) { continue; }
            var name = data[id];
            var form_id       = "{{ $dialog_id }}_" + id;
            var form_group_id = form_id + "_group";

            var html     = '<div id="' + form_group_id + '" class="col-3">                     ';
            html        += '<label for="' + form_id + '" class"">' + name + '</label>                               ';
            html        += '<input type="checkbox" value="' + id + '" class="checkboxradio {{ $input_class }}" id="' + form_id + '" data-customer_name="' + name + '">   '; 
            html        += '</div>                                                                             ';
            $('#{{ $prepend_id }}').before( html );
            customer_ids.push( id );
        }
        $('.checkboxradio').checkboxradio( { icon: false } );
    }

    /*
     *
     *　ダイヤログをクローズして、Hiddenフォームをペースト
     *
     */
    function {{ $dialog_id }}_dialog_close() {
        console.log( '{{ $dialog_id }}' );
    }

    /* 
     * 検索ダイアログの初期設定
     */
    $('#{{ $dialog_id }}').dialog( {
        autoOpen: false,
        modal: true,
        width: 750,
        buttons: [ {
            text: 'OK',
            icon: 'ui-icon-heart',
            click: function() {
                $(this).dialog( 'close' );
                {{ $dialog_id }}_dialog_close();
            }
        }],
        /*
         *　ダイヤログをクローズして、Hiddenフォームをペースト
         */
        close: function( event, ui ) {
            console.log( event, $(this), 'aaa' );
            var html = "";
            $('#{{ $hidden_id }}').html( '' );

            $('.{{ $input_class }}:checked').each( function() {
                console.log( $(this).val(), $(this).data( 'customer_name' ), $(this).prop('checked') );
                var customer_id   = $(this).val();
                var customer_name = $(this).data('customer_name');
                
                html += "<input type='hidden' name='{{ $form_name }}' value='" + customer_id + "'>";
                html += "<div class='{{ $form_class }} m-1'>" + customer_name + "</div>";
            });
            $('#{{ $hidden_id }}').html( html );

            
        }
        
    });

    /* 
     * 検索ボタンを押したらダイヤログが開く 
     */
    $('#{{ $button_id }}').on( 'click', function() {
        $('#{{ $dialog_id }}').dialog( 'open' );
    });
</script>
