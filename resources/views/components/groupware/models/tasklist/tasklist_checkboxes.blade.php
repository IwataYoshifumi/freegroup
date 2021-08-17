@php
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\TaskList;
use App\myHttp\GroupWare\Models\TaskProp;

$request = request();

$name_of_show_hidden_tasklists = "component_" . $name . "_show_hidden_tasklists";
$name_of_tasklist_permission   = "component_" . $name . "_tasklist_permission";

$btn_search_tasklists = "component_" . $name . "_btn_search_tasklists";
$prepend_tasklist_area = "component_" . $name . "_prepend_tasklist_area";
$array_of_tasklist_ids = "component_" . $name . "_array_tasklist_ids";

$tasklist_search_form        = "component_" . $name . "_tasklist_search_form";
$tasklist_search_form_opener = "component_" . $name . "_tasklist_search_form_opener";

$permissions = [ 'owner' => 'タスクリスト管理者', 'writer' => '予定追加可', 'reader' => '予定閲覧可' ];

$tasklist_check_toggler = "component_" . $name . "_tasklist_check_toggler";

if( empty( $request->$name_of_tasklist_permission )) { $request->$name_of_tasklist_permission = 'writer'; }

@endphp
<div>
    <div class="col-12 m-2">
        <div id="{{ $prepend_tasklist_area }}">{{ $prepend_tasklist_area }}</div>
        <div class="btn btn-sm btn-outline-dark" data-toggle="1" id="{{ $tasklist_check_toggler }}">全てチェックする</div>
        
        <div class="btn btn_icon" id="{{ $tasklist_search_form_opener }}" title="タスクリスト検索フォーム">@icon( caret-square-down )</div>

        <div id="{{ $tasklist_search_form }}">
            <label for="{{ $name_of_show_hidden_tasklists }}">非表示タスクリストも検索</label>
            {{ Form::checkbox( $name_of_show_hidden_tasklists, 1, $request->$name_of_show_hidden_tasklists , [ 'id' => $name_of_show_hidden_tasklists , 'class' => 'checkboxradio' ] ) }}
            {{ Form::select( $name_of_tasklist_permission, $permissions, $request->$name_of_tasklist_permission, [ 'id' => $name_of_tasklist_permission, 'class' => 'form-control' ] ) }}

            <div class="btn btn-outline-dark m-1" id="{{ $btn_search_tasklists }}">タスクリストを再検索</div>
        </div>
    </div>
    
    <script>
        //　選択されタスクリストID（ tasklist_id の配列）
        //
        let {{ $array_of_tasklist_ids }} = [{{ implode( ",", $tasklists ) }}];
        console.log( {{ $array_of_tasklist_ids }} );
        
        $("#{{ $tasklist_search_form_opener }}").on( 'click', function() {
            $("#{{ $tasklist_search_form }}").toggle( 'blind', { percent: 20 }, 200 );
        });
        
        //　タスクリスト検索AJAX
        //
        $('#{{ $btn_search_tasklists }}').on( 'click', function() {

            //　チェックされているタスクリストIDを更新
            //
            update_array_of_tasklist_ids();

            //　タスクリストを検索し、タスクリストリストを更新する
            //
            search_tasklists();        
        });
        
        function search_tasklists() {

            //　タスクリストの検索条件
            //
            if( $('#{{ $name_of_show_hidden_tasklists }}').prop( 'checked' )) {
                var show_hidden_tasklists = 1;
            } else {
                var show_hidden_tasklists = 0;
            }
            var tasklist_permission   = $('#{{ $name_of_tasklist_permission }}').val();
            
            // {{ $array_of_tasklist_ids }} = [];
            $('.{{ $name }}').each( function() {
                if( $(this).prop( 'checked' ) ) {
                     {{ $array_of_tasklist_ids }}.push( $(this).val() );
                }
            });
            console.log( show_hidden_tasklists, tasklist_permission, {{ $array_of_tasklist_ids }} );
            
            //　タスクリストを検索
            //
            $.ajax( {
                url : "{{ route( 'ajax.tasklist.search' ) }}",
                type: "GET",
                data: { show_hidden_tasklists : show_hidden_tasklists,
                        auth : tasklist_permission,
                        tasklists : {{ $array_of_tasklist_ids }},
                        user_id : {{ user_id() }}
                        }
            }).done( function( data, status, xhr ) {
                console.log( data, status );
                update_tasklists( data );
            
            }).fail( function( xhr, status, error ) {
            
            });
        }

        //　タスクリストチェックボックスを検索結果に基づき作画する
        //
        function update_tasklists( data ) {
            var prepend_tasklist_area = $("#{{ $prepend_tasklist_area }}");
            
            var html = ""
            data.forEach( function( tasklist, i ) {
                
                var id   = "{{ $name }}_tasklist_id_" + tasklist['id'];
                if( tasklist['type'] == 'company-wide' ) {
                    var name = tasklist['prop_name'] + " (全社)";
                } else {
                    var name = tasklist['prop_name'];
                }
                
                html += "<div style='background-color: " + tasklist['background_color'] + "; color: " + tasklist['text_color'] + ";'>";
                html += '<input name="{{ $name }}[]" type="checkbox" class="m-1 {{ $name }}" id="' + id + '" value=' + tasklist['id'] + '>';
                html += '<label for="' + id + '" style="cursor: pointer" class="m-1">' + name + '</label>'; 
                html += '<a class="btn btn-sm" href="{{ url( 'groupware/taskprop/show' ) }}/' + tasklist['taskprop_id'] + '"><i class="fas fa-cog"></i></a>';
                html += "</div>";
            });
            prepend_tasklist_area.html( '' );
            prepend_tasklist_area.html( html );
            check_tasklist_checkboxs();
        }
        
        //　元々チェックされたタスクリストをタスクリストリスト更新後に改めてチェックする
        //
        function check_tasklist_checkboxs() {
            $(".{{ $name }}").each( function() {
                console.log( {{ $array_of_tasklist_ids }}, $(this).val(),  {{ $array_of_tasklist_ids }}.includes( Number( $(this).val() ))  );

                if( {{ $array_of_tasklist_ids }}.includes( Number( $(this).val() ) ) ){
                    $(this).prop( 'checked', true );
                }
            });
        }
        
        //　チェックボックス選択・解除ボタン
        //
        $('#{{ $tasklist_check_toggler }}').on( 'click', function() {
            if( $(this).html() == "全てチェックする" ) {
                var checked = true;
                $(this).html( 'チェックを外す' );
            } else {
                var checked = false;
                $(this).html( '全てチェックする' );
            }
            $(".{{ $name }}").each( function() {
                $(this).prop( 'checked', checked );
            });
        });
        
        //　チェックされているタスクリストIDを配列へ格納する
        //
        function update_array_of_tasklist_ids() {
            {{ $array_of_tasklist_ids }} = [];
            $(".{{ $name }}").each( function() {
                console.log( {{ $array_of_tasklist_ids }}, $(this).val(), $(this).prop('checked' ));

                if( $(this).prop('checked' )){
                    {{ $array_of_tasklist_ids }}.push( Number( $(this).val() ));
                }
            });
        }
        
        
        $(document).ready( function() {
            $('#{{ $tasklist_search_form }}').hide();
            search_tasklists();
        });
        
    </script>
</div>