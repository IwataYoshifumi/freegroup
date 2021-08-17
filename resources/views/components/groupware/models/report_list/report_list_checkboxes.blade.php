@php
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\ReportList;
use App\myHttp\GroupWare\Models\CalProp;

$request = request();

$name_of_show_hidden_report_lists = "component_" . $name . "_show_hidden_report_lists";
$name_of_report_list_permission   = "component_" . $name . "_report_list_permission";

$btn_search_report_lists = "component_" . $name . "_btn_search_report_lists";
$prepend_report_list_area = "component_" . $name . "_prepend_report_list_area";
$array_of_report_list_ids = "component_" . $name . "_array_report_list_ids";

$report_list_search_form        = "component_" . $name . "_report_list_search_form";
$report_list_search_form_opener = "component_" . $name . "_report_list_search_form_opener";

$permissions = [ 'owner' => '日報リスト管理者', 'writer' => '予定追加可', 'reader' => '予定閲覧可' ];

$report_list_check_toggler = "component_" . $name . "_report_list_check_toggler";

if( empty( $request->$name_of_report_list_permission )) { $request->$name_of_report_list_permission = 'writer'; }

@endphp
<div>
    <div class="col-12 m-2">
        <div id="{{ $prepend_report_list_area }}">{{ $prepend_report_list_area }}</div>
        
        <div class="btn btn-sm btn-outline-dark" id="{{ $report_list_check_toggler }}">全てチェックする</div>
        <div class="btn btn_icon" id="{{ $report_list_search_form_opener }}">@icon( caret-square-down )</div>

        <div id="{{ $report_list_search_form }}">
            <label for="{{ $name_of_show_hidden_report_lists }}">非表示日報リストも検索</label>
            {{ Form::checkbox( $name_of_show_hidden_report_lists, 1, $request->$name_of_show_hidden_report_lists , [ 'id' => $name_of_show_hidden_report_lists , 'class' => 'checkboxradio' ] ) }}
            {{ Form::select( $name_of_report_list_permission, $permissions, $request->$name_of_report_list_permission, [ 'id' => $name_of_report_list_permission, 'class' => 'form-control' ] ) }}

            <div class="btn btn-outline-dark m-1" id="{{ $btn_search_report_lists }}">日報リストを再検索</div>
        </div>
    </div>
    
    <script>
        //　選択され日報リストID（ report_list_id の配列）
        //
        let {{ $array_of_report_list_ids }} = [{{ implode( ",", $report_lists ) }}];
        console.log( {{ $array_of_report_list_ids }} );
        
        $("#{{ $report_list_search_form_opener }}").on( 'click', function() {
            $("#{{ $report_list_search_form }}").toggle( 'blind', { percent: 20 }, 200 );
        });
        
        //　日報リスト検索AJAX
        //
        $('#{{ $btn_search_report_lists }}').on( 'click', function() {

            //　チェックされている日報リストIDを更新
            //
            update_array_of_report_list_ids();

            //　日報リストを検索し、日報リストリストを更新する
            //
            search_report_lists();        
        });
        
        function search_report_lists() {

            //　日報リストの検索条件
            //
            if( $('#{{ $name_of_show_hidden_report_lists }}').prop( 'checked' )) {
                var show_hidden_report_lists = 1;
            } else {
                var show_hidden_report_lists = 0;
            }
            var report_list_permission   = $('#{{ $name_of_report_list_permission }}').val();
            console.log( show_hidden_report_lists, report_list_permission );

            //　日報リストを検索
            //
            $.ajax( {
                url : "{{ route( 'ajax.report_list.search2' ) }}",
                type: "GET",
                data: { show_hidden_report_lists : show_hidden_report_lists,
                        report_list_permission   : report_list_permission,
                        user_id : {{ user_id() }}
                        }
            
            }).done( function( data, status, xhr ) {
                console.log( data, status );
                update_report_lists( data );
            
            }).fail( function( xhr, status, error ) {
            
            });
        }

        //　日報リストチェックボックスを検索結果に基づき作画する
        //
        function update_report_lists( data ) {
            var prepend_report_list_area = $("#{{ $prepend_report_list_area }}");
            
            var html = ""
            data.forEach( function( report_list, i ) {
                
                var id   = "{{ $name }}_report_list_id_" + report_list['id'];
                if( report_list['type'] == 'company-wide' ) {
                    var name = report_list['prop_name'] + " (全社)";
                } else {
                    var name = report_list['prop_name'];
                }
                
                html += "<div style='background-color: " + report_list['background_color'] + "; color: " + report_list['text_color'] + ";'>";
                html += '<input name="{{ $name }}[]" type="checkbox" class="m-1 {{ $name }}" id="' + id + '" value=' + report_list['id'] + '>';
                html += '<label for="' + id + '" style="cursor: pointer" class="m-1">' + name + '</label>'; 
                html += '<a class="btn btn-sm" href="{{ url( 'groupware/report_prop/show' ) }}/' + report_list['report_prop_id'] + '"><i class="fas fa-cog"></i></a>';
                html += "</div>";
            });
            prepend_report_list_area.html( '' );
            prepend_report_list_area.html( html );
            check_report_list_checkboxs();
        }
        
        //　元々チェックされた日報リストを日報リストリスト更新後に改めてチェックする
        //
        function check_report_list_checkboxs() {
            $(".{{ $name }}").each( function() {
                console.log( {{ $array_of_report_list_ids }}, $(this).val(),  {{ $array_of_report_list_ids }}.includes( Number( $(this).val() ))  );

                if( {{ $array_of_report_list_ids }}.includes( Number( $(this).val() ) ) ){
                    $(this).prop( 'checked', true );
                }
            });
        }
        
        //　チェックボックス選択・解除ボタン
        //
        $('#{{ $report_list_check_toggler }}').on( 'click', function() {
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
        
        //　チェックされている日報リストIDを配列へ格納する
        //
        function update_array_of_report_list_ids() {
            {{ $array_of_report_list_ids }} = [];
            $(".{{ $name }}").each( function() {
                console.log( {{ $array_of_report_list_ids }}, $(this).val(), $(this).prop('checked' ));

                if( $(this).prop('checked' )){
                    {{ $array_of_report_list_ids }}.push( Number( $(this).val() ));
                }
            });
        }
        
        
        $(document).ready( function() {
            $('#{{ $report_list_search_form }}').hide();
            search_report_lists();
        });
        
    </script>
</div>