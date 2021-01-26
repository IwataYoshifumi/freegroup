@php
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;

// 各変数の初期化
//

$form_name = $name . "[]";
$button_id = $name . "_component_checkboxes_users_opener";
$dialog_id = $name . "_component_checkboxes_users_dialog";
$input_class = $name . "_component_checkboxes_class";
$user_name_id  = $dialog_id . "_user_name";
$dept_name_id  = $dialog_id . "_depte_name";
$grade_name_id = $dialog_id . "_grade_name";

// 社員選択チェックボックス表示先ＩＤ
// 隠しフォーム挿入先ＩＤ
//
$prepend_id = $name . "_component_checkboxes_prepend";
$hidden_id  = $name . "_component_hidden_prepend";

// 社員検索のためのルート
//
$url   = route( 'ajax.user.search' );

//　同じ社員のチェックボックスを表示しないための変数
//  JavaScriptに変数として渡すために、カンマ区切り文字列に変換
// 
$user_ids = ( count( $users )) ? $users->pluck('id')->toArray() : [];
$user_ids = implode( ",", $user_ids );

@endphp

<div class="m-1">
    <div id="{{ $hidden_id }}" class="row">
        @foreach( $users as $user )        
            <input type='hidden' name='{{ $form_name }}' value='{{ $user->id }}'>
            <div class='{{ $form_class }} m-1'>{{ $user->name }}</div>
        @endforeach
    </div>
</div>

<div class="btn btn-outline-secondary" id='{{ $button_id }}'>{{ $button }}</div>



<div id='{{ $dialog_id }}' title="{{ $button }}">

    <div class="row">
        @php
        
        @endphp
        
        <div class="col-8 row">
            <label for="{{ $user_name_id }}" class="col">社員名：</label>
            {{ Form::text( 'user_name' , old( 'user_name'  ), [ 'class' => 'form-control col-8', 'id' => $user_name_id, 'autocomplete' => 'off', 'placeholder' => '社員名検索' ] ) }}
        </div>
        <div class="col-4 row container">
            <label for="{{ $grade_name_id }}" class="col-5">役職名：</label>
            {{ Form::text( 'grade_name', old( 'grade_name' ), [ 'class' => 'form-control col-7', 'id' => $grade_name_id, 'autocomplete' => 'off','placeholder' => '役職名検索' ] ) }}
        </div>
        <div class="col-8 row container mt-1">
            <label for="{{ $dept_name_id }}" class="col">部署名：</label>
            {{ Form::text( 'dept_name' , old( 'dept_name'  ), [ 'class' => 'form-control col-8', 'id' => $dept_name_id,  'autocomplete' => 'off','placeholder' => '部署名検索' ] ) }}
        </div>
        
        
        <div class="col-12 row container mt-1">
            <div class="col-2"></div>
            <button type=button class="btn btn-outline-dark btn-sm col-7 m-1" onClick="{{ $name }}_search_users()">社員を検索</button>
            <div class="btn btn-outline-dark btn-sm m-1" id='{{ $button_id }}_clear'>選択解除</div>
        </div>
    </div>
    <hr>

    <div class="row">
        @foreach( $users as $user )
            @php
                $form_id = $dialog_id ."_" . $user->id;
                $form_group_id = $form_id . "_group";
            @endphp
            <div id="{{ $form_group_id }}" class="col-4">
                <label for="{{ $form_id }}" class"w-100">【{{ $user->dept->name }} {{ $user->grade }}】{{ $user->name }}</label>
                <input type="checkbox", name="{{ $form_name }}" value="{{ $user->id }}" checked class="checkboxradio {{ $input_class }}" id="{{ $form_id }}" data-user_name="{{ $user->name }}">
                {{-- Form::checkbox( $form_name, $user->id, true, [ 'class' => 'checkboxradio', 'id' => $form_id ] ) --}}
            </div>
        @endforeach
        <div id="{{ $prepend_id }}"></div>
    </div>
</div>
<!--
/*
 *
 *　ここから社員選択フォームのスクリプト
 *
 */
-->
<script>
    /*
     *　社員検索
     */
    function {{ $name }}_search_users() {
        var url = '{{ $url }}';
        var user_name  = $('#{{ $user_name_id  }}').val();
        var dept_name  = $('#{{ $dept_name_id  }}').val();
        var grade_name = $('#{{ $grade_name_id }}').val();
        
        var token = '{{ csrf_token() }}';

        console.log( user_name );

        $.ajax(
            url, {
            ttype: 'GET',
            data: { user_name: user_name, dept_name: dept_name, grade_name: grade_name } 
        }).done( function( data, status, xhr ) {
            console.log( data, status,xhr  );
            {{ $name }}_prepend_checkboxes( data );

        }).fail( function( xhr, status, error ) {
            console.log( status, error, xhr );
            alert( 'エラーで削除できませんでした');
        }); 
    }

    /*
     *　グローバル変数
     *  フォーム内に表示れているチェックボックスの社員ＩＤの配列
     *  同じ社員がフォームに表示されないようにチェック
     */
    var user_ids = [ {{ $user_ids }} ];

    /*
     *　検索結果から社員選択チェックボックスを生成
     */
    function {{ $name }}_prepend_checkboxes( data ) {
        //console.log( user_ids );
        
        if( data.length == 0 ) { alert( 'この条件では何も検索できませんでした' ); return; }
        
        data.forEach( function( user, i  ) {     
            //console.log( i, user['id'], user['name'], user['grade'], user['dept_name'] );
            var user_id = user['id'];
            var in_array = user_ids.some( function( value ) { return ( value == user_id ) } ); 
            if( ! in_array ) { 
                var name  = user['name'];
                var dept  = user['dept_name'];
                var grade = user['grade'];

                var form_id       = "{{ $dialog_id }}_" + user_id;
                var form_group_id = form_id + "_group";
    
                var html     = '<div id="' + form_group_id + '" class="col-4">';
                html        += '<label for="' + form_id + '" class"">【' + dept + ' ' + grade + '】 '   + name + '</label>';
                html        += '<input type="checkbox" value="' + user_id + '" class="checkboxradio {{ $input_class }}" id="' + form_id + '" data-user_name="' + name + '">   '; 
                html        += '</div>                                                                             ';
                $('#{{ $prepend_id }}').before( html );

                user_ids.push( user_id );
            }
        });
        $('.checkboxradio').checkboxradio( { icon: false } );
    }


    /* 
     * 社員検索ダイアログの設定
     */
    $('#{{ $dialog_id }}').dialog( {
    
        /*
         *　社員検索ダイアログの初期設定
         */
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
         *　社員検索ダイアログ　クローズイベントの処理
         *　社員検索ダイヤログをクローズしたら、社員ＩＤの隠しフォームをペースト
         */
        close: function( event, ui ) {
            //console.log( event, $(this), 'aaa' );
            var html = "";
            $('#{{ $hidden_id }}').html( '' );

            $('.{{ $input_class }}:checked').each( function() {
                //console.log( $(this).val(), $(this).data( 'user_name' ), $(this).prop('checked') );
                var user_id   = $(this).val();
                var user_name = $(this).data('user_name');
                
                html += "<input type='hidden' name='{{ $form_name }}' value='" + user_id + "'>";
                html += "<div class='{{ $form_class }} m-1'>" + user_name + "</div>";
            });
            $('#{{ $hidden_id }}').html( html );
        }
        
    });

    /* 
     * 社員検索ダイヤログ開くボタン 
     */
    $('#{{ $button_id }}').on( 'click', function() {
        $('#{{ $dialog_id }}').dialog( 'open' );
    });

    /* 
     * 選択解除ボタン
     */
    $('#{{ $button_id }}_clear').on( 'click', function() {
        $('.{{ $input_class }}').each( function() {
            if( $(this).prop("checked") ) { $(this).click(); }
        });
    });

    /*
     * 社員検索ダイヤログ閉じるボタン
     */
    function {{ $dialog_id }}_dialog_close() {
        console.log( '{{ $dialog_id }}' );
    }


</script>
