@php
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Calendar;
use App\myHttp\GroupWare\Models\CalProp;

$request = request();

$name_of_show_hidden_calendars = "component_" . $name . "_show_hidden_calendars";
$name_of_calendar_permission   = "component_" . $name . "_calendar_permission";

$value_of_calendar_permission = ( ! empty( $reqeust->$name_of_calendar_permission )) ? $request->$name_of_calendar_permission : "writer";

$btn_search_calendars = "component_" . $name . "_btn_search_calendars";
$prepend_calendar_area = "component_" . $name . "_prepend_calendar_area";
$array_of_calendar_ids = "component_" . $name . "_array_calendar_ids";

$calendar_search_form        = "component_" . $name . "_calendar_search_form";
$calendar_search_form_opener = "component_" . $name . "_calendar_search_form_opener";

$permissions = [ 'owner' => 'カレンダー管理者', 'writer' => '予定追加可', 'reader' => '予定閲覧可' ];

$calendar_check_toggler = "component_" . $name . "_calendar_check_toggler";


@endphp
<div>
    <div class="col-12 m-2">
        <div id="{{ $prepend_calendar_area }}">{{ $prepend_calendar_area }}</div>
        
        <div class="btn btn-sm btn-outline-dark" id="{{ $calendar_check_toggler }}">全てチェックする</div>
        <div class="btn btn_icon" id="{{ $calendar_search_form_opener }}">@icon( caret-square-down )</div>

        <div id="{{ $calendar_search_form }}">
            <label for="{{ $name_of_show_hidden_calendars }}">非表示カレンダーも検索</label>
            {{ Form::checkbox( $name_of_show_hidden_calendars, 1, $request->$name_of_show_hidden_calendars , [ 'id' => $name_of_show_hidden_calendars , 'class' => 'checkboxradio' ] ) }}
            {{ Form::select( $name_of_calendar_permission, $permissions, $value_of_calendar_permission, [ 'id' => $name_of_calendar_permission, 'class' => 'form-control' ] ) }}

            <div class="btn btn-outline-dark m-1" id="{{ $btn_search_calendars }}">カレンダーを再検索</div>
        </div>
    </div>
    
    <script>
        //　選択されカレンダーID（ calendar_id の配列）
        //
        let {{ $array_of_calendar_ids }} = [{{ implode( ",", $calendars ) }}];
        
        console.log( {{ $array_of_calendar_ids }} );
        
        $("#{{ $calendar_search_form_opener }}").on( 'click', function() {
            $("#{{ $calendar_search_form }}").toggle( 'blind', { percent: 20 }, 200 );
        });
        
        //　カレンダー検索AJAX
        //
        $('#{{ $btn_search_calendars }}').on( 'click', function() {

            //　チェックされているカレンダーIDを更新
            //
            update_array_of_calendar_ids();

            //　カレンダーを検索し、カレンダーリストを更新する
            //
            search_calendars();        
        });
        
        function search_calendars() {

            //　カレンダーの検索条件
            //
            if( $('#{{ $name_of_show_hidden_calendars }}').prop( 'checked' )) {
                var show_hidden_calendars = 1;
            } else {
                var show_hidden_calendars = 0;
            }
            var calendar_permission   = $('#{{ $name_of_calendar_permission }}').val();
            
            // {{ $array_of_calendar_ids }} = [];
            $('.{{ $name }}').each( function() {
                if( $(this).prop( 'checked' )) {
                    {{ $array_of_calendar_ids }}.push( $(this).val() )
                }
            });
                        
            
            console.log( show_hidden_calendars, calendar_permission, {{ $array_of_calendar_ids }} );
            

            //　カレンダーを検索
            //
            $.ajax( {
                url : "{{ route( 'ajax.calendar.search' ) }}",
                type: "GET",
                data: { show_hidden_calendars : show_hidden_calendars,
                        calendar_permission   : calendar_permission,
                        calendars             : {{ $array_of_calendar_ids }},
                        user_id : {{ user_id() }}
                        }
            
            }).done( function( data, status, xhr ) {
                console.log( data, status );
                update_calendars( data );
            
            }).fail( function( xhr, status, error ) {
            
            });
        }

        //　カレンダーチェックボックスを検索結果に基づき作画する
        //
        function update_calendars( data ) {
            var prepend_calendar_area = $("#{{ $prepend_calendar_area }}");
            
            var html = ""
            data.forEach( function( calendar, i ) {
                
                var id   = "{{ $name }}_calendar_id_" + calendar['id'];
                if( calendar['type'] == 'company-wide' ) {
                    var name = calendar['prop_name'] + " (全社)";
                } else {
                    var name = calendar['prop_name'];
                }
                
                html += "<div class='d-flex' style='background-color: " + calendar['background_color'] + "; color: " + calendar['text_color'] + ";'>";
                html += '<input name="{{ $name }}[]" type="checkbox" class="m-1 {{ $name }}" id="' + id + '" value=' + calendar['id'] + '>';
                html += '<label for="' + id + '" style="cursor: pointer" class="m-1 text-truncate" title="' + name + '">' + name + '</label>'; 
                html += '<a class="btn btn-sm" href="{{ url( 'groupware/calprop/show' ) }}/' + calendar['calprop_id'] + '"><i class="fas fa-cog"></i></a>';
                html += "</div>";
            });
            prepend_calendar_area.html( '' );
            prepend_calendar_area.html( html );
            check_calendar_checkboxs();
        }
        
        //　元々チェックされたカレンダーをカレンダーリスト更新後に改めてチェックする
        //
        function check_calendar_checkboxs() {
            $(".{{ $name }}").each( function() {
                console.log( {{ $array_of_calendar_ids }}, $(this).val(),  {{ $array_of_calendar_ids }}.includes( Number( $(this).val() ))  );

                if( {{ $array_of_calendar_ids }}.includes( Number( $(this).val() ) ) ){
                    $(this).prop( 'checked', true );
                }
            });
        }
        
        //　チェックボックス選択・解除ボタン
        //
        $('#{{ $calendar_check_toggler }}').on( 'click', function() {
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
        
        //　チェックされているカレンダーIDを配列へ格納する
        //
        function update_array_of_calendar_ids() {
            {{ $array_of_calendar_ids }} = [];
            $(".{{ $name }}").each( function() {
                console.log( {{ $array_of_calendar_ids }}, $(this).val(), $(this).prop('checked' ));

                if( $(this).prop('checked' )){
                    {{ $array_of_calendar_ids }}.push( Number( $(this).val() ));
                }
            });
        }
        
        
        $(document).ready( function() {
            $('#{{ $calendar_search_form }}').hide();
            search_calendars();
        });
        
    </script>
</div>