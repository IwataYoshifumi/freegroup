@php
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\AccessList;





//　Blade内限定クラス（変数の初期化、変数のスコープ対策）
//
class component_input {

    //　フォームの名前
    //
    public $form_name;

    //　コンポーネントクラスからの入力値
    //
    public $report_lists;
    public $report_list_ids;

    // JavaScript で利用するクラス名, ID名
    //
    public $component_id;
    public $name_id;
    public $users_id;
    public $type_id;
    public $auth_id;
    public $search_btn_id;
    public $prepend_area_id;
    public $report_lists_id;

    public $report_list_form_id;
    public $hidden_form_area_id;

    //　検索フォームの入力値
    //
    public $users;
    public $name;
    
    public $report_list_types;
    public $report_list_auths;

    public $url;
    

    public function __construct( $name, $report_lists ) {
    
        $this->form_name = $name;

        //　本フォームの入力値
        //
        $this->report_lists = $report_lists;

    
        $request = request();
    
        $this->component_id = 'checkboxes_report_list_componet_' . $name . '_';
        $id = $this->component_id;


        //　各フォームのID
        //
        $this->name_id          = $id . 'name';
        $this->users_id         = $id . 'users';
        $this->type_id          = $id . 'type';
        $this->auth_id          = $id . 'auth';
        $this->show_hidden_id   = $id . 'show_hidden';
        $this->show_disabled_id = $id . 'show_disabled';

        $this->search_btn_id    = $id . 'search_btn';
        $this->dialog_opener_id = $id . 'dialog_opener';
        $this->dialog_id        = $id . 'dialog_id';
        
        $this->prepend_area_id = $id . 'prepend_area_id';

        $this->report_lists_form_id = $id . 'report_lists_form_id';

        $this->hidden_form_area_id  = $id . 'hidden_form_area_id';
        $this->hidden_form_id       = $id . 'hidden_form_id';

        //　各フォームの初期値
        //
        $this->name_value   = $request->input( $this->name_id );
        $this->users_values = ( $request->input( $this->users_id ) ) ? $request->input( $this->users_id ) : [ user_id() ]; 
        $this->type_values  = ( $request->input( $this->type_id  ) ) ? $request->input( $this->type_id  ) : [];
        $this->auth_value   = $request->input( $this->auth_id );
        $this->show_hidden_value   = $request->input( $this->show_hidden_id   );
        $this->show_disabled_value = $request->input( $this->show_disabled_id );

        //　各フォームの値
        //
        $this->report_list_types = ReportList::getTypes();
        $this->report_list_auths = [  'owner' => '管理者', 'canWrite' => '日報追加可能', 'canRead' => '日報閲覧のみ' ];

        $report_list_ids = ( count( $report_lists )) ? $report_lists->pluck('id')->toArray() : [];
        $this->report_list_ids = implode( ",", $report_list_ids );


        // AJAX通信の接続先URL
        //
        $this->url = route( 'ajax.report_list.search' );

    }
}

//　Blade内使用変数の初期化
//
$input = new component_input( $name, $report_lists );
# if_debug( $input );

@endphp

{{-- 検索ダイヤログ表示ボタン --}}

<div class="btn btn-outline-secondary" id='{{ $input->dialog_opener_id }}'>{{ $button }}</div>

{{-- 日報リスト　隠しフォーム表示エリア --}}

<div id='{{ $input->hidden_form_area_id }}'>
    @foreach( $input->report_lists as $report_list )
        <input type="hidden" name="{{ $input->form_name }}[]" value="{{ $report_list->id }}">
        <div class="{{ $form_class }} m-1">{{ $report_list->name }}</div>
    @endforeach
</div>

{{-- ReportList検索用ダイヤログ --}}

<div class="border border-dark m-1 p-1" id="{{ $input->dialog_id }}">
    <div class="row container">
        <div class="col-3 m-1 border border-dark container">
            <div>日報リスト名</div>
            {{ Form::text( $input->name_id, old( $input->name_id ), [ 'class' => 'form-control', 'id' => $input->name_id ] ) }}
        </div>
        
        {{-- ユーザ検索 --}}
        <div class="col-4 border border-dark m-1 p-1">
                <div class="m-2">日報リストアクセス権限検索</div>
                @foreach( $input->report_list_auths as $user_auth => $name ) 
                    @php
                        $id      = $input->auth_id . "_" . $user_auth;
                        $checked =  ( 1 ) ? 1 : 0;
                    @endphp
                    <label for="{{ $id }}">{{ $name }}</label>
                    {{ Form::radio( $input->auth_id, $user_auth, $checked, [ 'class' => $input->auth_id . ' checkboxradio', 'id' => $id ] ) }}
                @endforeach

                <div class="m-2">検索対象社員<span title="自分は必ず検索対象に含まれます" class="m-1 uitooltip">@icon( info-circle )</span></div>
                <x-checkboxes_users :users="$input->users_values" name='{{ $input->users_id }}' button="社員検索" class="{{ $input->users_id }}"/>
        </div>
    
        
        <div class="col-3 m-1 border border-dark container">
            <div class="m-2">検索対象日報リスト</div>

            <div class="m-1">公開種別</div>
            @foreach( $input->report_list_types as $type => $name )
                <label for="{{ $input->type_id }}_{{ $type }}">{{ $name }}</label>
                {{ Form::checkbox( $input->type_id ."[]", $type,  false , 
                        [   'id'    => $input->type_id . "_". $type, 
                            "class" => "$input->type_id checkboxradio"
                            ] ) }}
            @endforeach
        
            <div class="m-2">その他の条件</div>
            <label for="{{ $input->show_hidden_id }}">非表示日報リストも検索</label>
            {{ Form::checkbox( $input->show_hidden_id,  1,  false, [ 'id' => $input->show_hidden_id, "class" => "checkboxradio m-1" ] ) }}
            
            <label for="{{ $input->show_disabled_id }}">無効日報リストも検索</label>
            {{ Form::checkbox( $input->show_disabled_id, 1,  false, [ 'id' => $input->show_disabled_id, "class" => "checkboxradio m-1" ] ) }}
            
        </div>
        <div class="col-12 m-1">
            <div class="btn btn-secondary m-1" id='{{ $input->search_btn_id }}'>検索</div>
        </div>
    </div>
    
    {{-- 日報リスト選択フォーム --}}
    <hr>
    <div class="row border border-dark m-1">
        @foreach( $report_lists as $report_list )
            @php
                $form_id       = $input->report_lists_form_id . "_" . $report_list->id;
                $form_group_id = $form_id . "_group";
            @endphp
            <div id="{{ $form_group_id }}" class="col-3 m-1">
                <label for="{{ $form_id }}" class"w-100">{{ $report_list->name }}</label>
                <input type="checkbox" 
                    name="{{ $input->report_lists_form_id }}[]" 
                    value="{{ $report_list->id }}" 
                    checked 
                    class="checkboxradio {{ $input->report_lists_form_id }}" 
                    id="{{ $form_id }}" 
                    data-name="{{ $report_list->name }}">
            </div>
        @endforeach
        <div id="{{ $input->prepend_area_id }}"></div>

    </div>
</div>

{{-- 検索ダイヤログ　終了 --}}



<script>
    /*
     *　検索ボタンの動作
     */
    $('#{{ $input->search_btn_id }}').on( 'click', function() {
        var url  = '{{ $input->url }}';

        /*
         *
         * 検索条件の取得
         *
         */
        var name          = $('#{{ $input->name_id }}').val();
        var show_hidden   = $('#{{ $input->show_hidden_id }}').prop( 'checked'   ) ? 1 : 0;
        var show_disabled = $('#{{ $input->show_disabled_id }}').prop( 'checked' ) ? 1 : 0;
        
        var user_auth = '';
        $('.{{ $input->auth_id }}').each( function() {
            // console.log( $(this).val(), $(this).prop('checked') );
            if( $(this).prop('checked')) {
                user_auth = $(this).val();
                return false;
            }
        });
        var users = [];
        $('input[name="{{ $input->users_id }}[]"]').each( function() {
            // console.log( $(this).val() );
            if( $(this).prop( 'checked' ) ) {
                users.push( $(this).val() );
            }
        });
        var types = [];
        $('.{{ $input->type_id }}').each( function() {
            // console.log( $(this).val() );
            if( $(this).prop( 'checked' )) {
                types.push( $(this).val() );
            }
        });
        
        // console.log( url, name, users, user_auth, show_hidden, show_disabled, types );

        /*
         *
         * 日報リストを検索
         *
         */
        $.ajax( 
            url, {
            ttype: 'GET',
            data: { name: name, users: users, user_auth: user_auth, show_hidden: show_hidden, show_disabled: show_disabled, types: types } 
        }).done( function( data, status, xhr ) {
            // console.log( data, status,xhr  );
            {{ $input->component_id }}_prepend_checkboxes( data );

        }).fail( function( xhr, status, error ) {
            console.log( status, error, xhr );
            alert( 'エラーで検索できませんでした');
        }); 
    });
    
    /*
     *　グローバル変数
     *  フォーム内に表示れているチェックボックスのReportList の配列
     *  同じReportListがフォームに表示されないようにチェック
     */
    var report_list_ids = [ {{ $input->report_list_ids }} ];
    
    /*
     *　検索結果から日報リスト選択用チェックボックスを生成
     */
    function {{ $input->component_id }}_prepend_checkboxes( data ) {
        
        if( data.length == 0 ) { alert( 'この条件では何も検索できませんでした' ); return; }
        
        data.forEach( function( d, i ) {
            console.log( i, d['id'], d['name'], d['type'] );
            var report_list_id   = d['id'];
            
            var in_array = report_list_ids.some( function( value ) { return ( value == report_list_id ) } );
            
            if( ! in_array ) {
            
                var name = d['name'];
                var type = d['type'];
                
                var form_id       = "{{ $input->report_lists_form_id }}" + report_list_id;
                var form_group_id = form_id + "_group";
        
                var html     = '<div id="' + form_group_id + '" class="col-3">';
                html        += '<label for="' + form_id + '" class"">'   + name + '</label>';
                html        += '<input type="checkbox" value="' + report_list_id + '" class="checkboxradio {{ $input->report_lists_form_id }}" id="' + form_id + '" data-name="' + name + '">   '; 
                html        += '</div>                                                                             ';
                $('#{{ $input->prepend_area_id }}').before( html );
                
                report_list_ids.push( report_list_id );
            }
        });
        $('.checkboxradio').checkboxradio( { icon: false } );        
        
    }
    
    /*
     * ReportListダイヤログ開くボタン
     */
    $('#{{ $input->dialog_opener_id }}').on( 'click', function() {
        $('#{{ $input->dialog_id }}').dialog( 'open' );
    });
    
    /*
     *
     * ReportListダイアログ
     *
     */
     $('#{{ $input->dialog_id }}').dialog( {
        autoOpen: false,
        modal: true,
        width: 980,
        buttons: [ {
            text: 'OK',
            icon: 'ui-icon-heart',
            click: function() {
                $(this).dialog( 'close' );
            }
        }],
        close: function( event, ui ) {
            console.log( event );
            $('#{{ $input->hidden_form_area_id }}').html('');
            
            var html = '';
            $('.{{ $input->report_lists_form_id }}:checked').each( function() {
                var report_list_id   = $(this).val();
                var report_list_name = $(this).data('name');
                console.log( report_list_id, report_list_name );
                
                html += "<input type='hidden' name='{{ $input->form_name }}[]' value='" + report_list_id + "'>";
                html += "<div class='{{ $form_class }} m-1'>" + report_list_name + "</div>";
            });
            $('#{{ $input->hidden_form_area_id }}').html( html );
        }

     });
    
    
    
</script>