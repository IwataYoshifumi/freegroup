@php
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;
use App\myHttp\GroupWare\Models\AccessList;

//　表示するタスクリストの情報を検索
//
if( count( $values )) {
    $tasklists = TaskList::whereCanRead( user_id() )->whereIn( 'id', $values );
} else {
    $tasklists = TaskList::whereCanWrite( user_id() );
}
$tasklists = $tasklists->with( [ 'taskprops' => function( $query ) {
                                $query->where( 'user_id', user_id() );
                            } ])->get();

$types = TaskList::getTypes();


$request = request();

$type_form_name = $form_name . "_type";
$types_values = ( is_array( $request->$type_form_name )) ? $request->$type_form_name : [];


// $tasklist_auths = [ '' => '', 'owner' => 'タスクリスト管理権限あり', 'writer' => 'タスクリストへタスク追加可能', 'reader' => 'タスク閲覧可能' ];
$tasklist_auths = [ 'owner' => 'タスクリスト管理権限あり', 'writer' => 'タスクリストへタスク追加可能', 'reader' => 'タスク閲覧可能' ];
$auth_form_name = $form_name . "_auth";
$auth_value     = ( $request->$auth_form_name ) ? $request->$auth_form_name : 'writer';

$hidden_form_name = $form_name . "_hidden";
$hidden_form_id   = $hidden_form_name;
$hidden_checked   = ( $request->$hidden_form_name ) ? 1 : 0;

$disabled_form_name = $form_name . "_disabled";
$disabled_form_id   = $disabled_form_name;
$disabled_checked   = ( $request->$disabled_form_name ) ? 1 : 0; 

$search_tasklist_button = $form_name . "_search_button";

$show_form_button = $form_name . "_show_tasklist_search_form_button";
$search_form      = $form_name . "_tasklist_search_form";

@endphp

{{-- 検索ダイヤログ表示ボタン --}}

{{-- タスクのステータス（完了・未完・保留） --}}

<div class="m-2 btn btn-sm bg-light" id="{{ $show_form_button }}">タスクリスト検索</div>


<div class="w-95 border border-dark bg-light m-1 p-1 container" id="{{ $search_form }}">
    <div class="row m-1">
        <span class="col-3">公開種別：</span>
        @foreach( $types as $type => $type_desc ) 
            @php
                $checked = ( in_array( $type, $types_values )) ? 1 : 0;
                $id = $form_id . "_" . $type;
            @endphp
    
            <label for="{{ $id }}">{{ $type_desc }}</label>
            {{ Form::checkbox( $type_form_name."[]", $type, $checked, [ 'class' => 'checkboxradio '. $type_form_name, 'id' => $id ] ) }}
        
        @endforeach
        
        <div class="col-12 m-1"></div>
    
        <span class="col-3">アクセス権限：</span>
        {{ Form::select( $auth_form_name,  $tasklist_auths, $auth_value, [ 'class' => 'form-control col-5', 'id' => $auth_form_name ] ) }}

        <div class="col-12 m-1"></div>
    
        <div class="col-3">その他の条件</div>
        <label for="{{ $hidden_form_id }}">非表示タスクリストを検索</label>
        {{ Form::checkbox( $hidden_form_name, 1,  $hidden_checked, [ 'id' => $hidden_form_id, "class" => "checkboxradio m-1 m-3" ] ) }}
            
        <label for="{{ $disabled_form_id }}">無効タスクリストを検索</label>
        {{ Form::checkbox( $disabled_form_name, 1,  $disabled_checked, [ 'id' => $disabled_form_id, "class" => "checkboxradio m-3" ] ) }}
    
        <div class="col-12 m-1"></div>
    
        <div class="col-1 m-1"></div>
        <a class="btn btn-outline-dark bg-white col-3 m-1" id="{{ $search_tasklist_button }}">タスクリストを検索</a>
    </div>
</div>

<div class="col-12"></div>
        
<div class="w-95 container">
    <div id="{{ $form_name }}_prepend_tasklists">
        @foreach( $tasklists as $tasklist )
            @php
                $id = $form_id . "_" . $tasklist->id;
                #dd( $values );
                $checked = ( in_array( $tasklist->id, $values )) ? true : false;
                
            @endphp
            <label for="{{ $id }}">{{ $tasklist->taskprops->first()->name }}</label>
            {{ Form::checkbox( $form_name . "[]", $tasklist->id, $checked, [ 'class' => 'checkboxradio', 'id' => $id ] ) }}
        @endforeach
    </div>

    <script>

        //　タスクリスト検索フォーム表示ボタン
        //
        var form_button = $('#{{ $show_form_button }}');
        var search_form = $('#{{ $search_form }}');

        form_button.on( 'click', function() {
            search_form.toggle( 'blind', { persent: 50 }, 200 );
        });
        $(document).ready( function() {
            search_form.hide();
        });


        //　タスクリストの検索処理（AJAX）
        //
        
        let search_tasklist_button = $('#{{ $search_tasklist_button }}');
        search_tasklist_button.on( 'click', function() {

            // 公開種別の値を取得
            //
            var types = [];
            var tasklist_types = $('.{{ $type_form_name }}');
            tasklist_types.each( function() {
                if( $(this).prop( 'checked' ) ) {
                    types.push( $(this).val() );
                    console.log( $(this).val() );
                }
            });

            //　アクセス権の値を取得
            //
            var auth_select = $('#{{ $auth_form_name }}');
            var auth = auth_select.val();
            
            //　非表示・無効タスクの検索条件の取得
            //
            var hidden;
            if( $('#{{ $hidden_form_id   }}').prop( 'checked' ) ) {
                hidden = 1 
            } else {
                hidden = 0;
            }

            var disabled; 
            if( $('#{{ $disabled_form_id }}').prop( 'checked') ) {
                disabled = 1;
            } else {
                disabled = 0;
            }

            console.log( types, auth, hidden, disabled );
            
            //　AjaxでTaskListを検索
            //
            $.ajax( {
                url: "{{ route( 'ajax.tasklist.search' ) }}",
                type: "GET",
                data: { types: types,
                        auth: auth,
                        hidden: hidden,
                        disabled: disabled,
                        user_id: {{ user_id() }}
                    }
            
            }).done( function( data, status, xhr ) {
                console.log( status );
                update_tasklists( data );

            
            });    
        });
        
        function update_tasklists( data ) {

            console.log( data.length, Array.isArray(data) );
            
            var prepend_tasklists = $('#{{ $form_name }}_prepend_tasklists');

            var html = "";
            data.forEach( function( tasklist, i ) {

                console.log( i, tasklist['id'], tasklist['name'], tasklist['prop_name'] );

                var id   = "{{ $form_name }}_tasklists_id_" + tasklist['id'];
                html += '<label for="' + id + '">' + tasklist['prop_name'] + '</label>';
                html += '<input name="{{ $form_name }}[]" type="checkbox" class="checkboxradio" id="' + id + '" checked value=' + tasklist['id'] + '>';
            });
            prepend_tasklists.html('');
            prepend_tasklists.html( html );
            
            $('.checkboxradio').checkboxradio( { icon: false } );
        }
        
    </script>
</div>

