@php
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;

$form_name = $name . "[]";
$button_id = $name . "_component_checkboxes_depts_opener";
$dialog_id = $name . "_component_checkboxes_depts_dialog";
$input_class = $name . "_component_checkboxes_class";
$dept_name_id = $dialog_id . "_dept_name";

$prepend_id = $name . "_component_checkboxes_prepend";
$hidden_id  = $name . "_component_hidden_prepend";

#if_debug( $form_name, $button_id, $dialog_id );

$url   = route( 'ajax.dept.search' );

$dept_ids = ( count( $depts )) ? $depts->pluck('id')->toArray() : [];
$dept_ids = implode( ",", $dept_ids );

$me = auth( 'user' )->user()->load( 'dept' );

@endphp

<div class="m-1">
    <div id="{{ $hidden_id }}" class="row">
        @foreach( $depts as $dept )        
            <input type='hidden' name='{{ $form_name }}' value='{{ $dept->id }}'>
            <div class='{{ $form_class }} m-1'>{{ $dept->name }}</div>
        @endforeach
    </div>
</div>

<div class="btn btn-outline-secondary" id='{{ $button_id }}'>{{ $button }}</div>

<div id='{{ $dialog_id }}' title="{{ $button }}">

    <div class="row">
        {{ Form::text( 'dept_name', old( 'dept_name' ), [ 'class' => 'form-control col-6 m-1', 'id' => $dept_name_id, 'placeholder' => '部署名検索' ] ) }}
        <button type=button class="btn btn-outline-dark btn-sm col-3 m-1" onClick="{{ $name }}_search_depts()">検索</button>
        <div class="col-12"></div>
        <button type=button class="btn btn-outline-dark btn-sm col-4 m-1" onClick="{{ $name }}_paste_dept()">「{{ $me->dept->name }}」で検索</button>

        <script>
            function {{ $name }}_paste_dept() {
                $('#{{ $dept_name_id }}').val( '{{ $me->dept->name }}');
                {{ $name }}_search_depts();
            }
        </script>
        
    </div>
    <hr>

    <div class="row">
        @foreach( $depts as $dept )
            @php
                $form_id = $dialog_id ."_" . $dept->id;
                $form_group_id = $form_id . "_group";
            @endphp
            <div id="{{ $form_group_id }}" class="col-3">
                <label for="{{ $form_id }}" class"w-100">{{ $dept->name }}</label>
                <input type="checkbox", name="{{ $form_name }}" value="{{ $dept->id }}" checked class="checkboxradio {{ $input_class }}" id="{{ $form_id }}" data-dept_name="{{ $dept->name }}">
                {{-- Form::checkbox( $form_name, $dept->id, true, [ 'class' => 'checkboxradio', 'id' => $form_id ] ) --}}
            </div>
        @endforeach
        <div id="{{ $prepend_id }}"></div>

    </div>
    

</div>

<script>
    // $('#{{ $dept_name_id }}').on( 'change', function() { search_depts(); });

    /*
     *
     *　部署を検索
     *
     */
    function {{ $name }}_search_depts() {
        var url = '{{ $url }}';
        var dept_name = $('#{{ $dept_name_id }}').val();
        var token = '{{ csrf_token() }}';

        $.ajax(
            url, {
            ttype: 'GET',
            data: { dept_name: dept_name } 
        }).done( function( data, status, xhr ) {
            console.log( data, status,xhr  );
            {{ $name }}_prepend_checkboxes( data );

        }).fail( function( xhr, status, error ) {
            console.log( status, error, xhr );
            alert( 'エラーで削除できませんでした');
        }); 
    }

    var dept_ids = [ {{ $dept_ids }} ]; 
    /*
     *
     *　検索結果から部署選択チェックボックスを生成
     *
     */
    function {{ $name }}_prepend_checkboxes( data ) {

        if( data.length == 0 ) { alert( 'この条件では何も検索できませんでした' ); return; }

        for( var id in data ) {
            var in_array = dept_ids.some( function( value ) { return ( value == id ) } ); 
            if( in_array ) { continue; }
            var name = data[id];
            var form_id       = "{{ $dialog_id }}_" + id;
            var form_group_id = form_id + "_group";

            var html     = '<div id="' + form_group_id + '" class="col-3">                     ';
            html        += '<label for="' + form_id + '" class"">' + name + '</label>                               ';
            html        += '<input type="checkbox" value="' + id + '" class="checkboxradio {{ $input_class }}" id="' + form_id + '" data-dept_name="' + name + '">   '; 
            html        += '</div>                                                                             ';
            $('#{{ $prepend_id }}').before( html );
            dept_ids.push( id );
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
                console.log( $(this).val(), $(this).data( 'dept_name' ), $(this).prop('checked') );
                var dept_id   = $(this).val();
                var dept_name = $(this).data('dept_name');
                
                html += "<input type='hidden' name='{{ $form_name }}' value='" + dept_id + "'>";
                html += "<div class='{{ $form_class }} m-1'>" + dept_name + "</div>";
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
