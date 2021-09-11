@php
use App\myHttp\GroupWare\Models\User;
use App\myHttp\GroupWare\Models\Dept;
use App\myHttp\GroupWare\Models\Facility;

$request = request();

#if_debug( $request );


$name_of_show_hidden_facilities = "component_" . $name . "_show_hidden_facilities";
$name_of_facility_permission   = "component_" . $name . "_facility_permission";

$value_of_facility_permission = ( ! empty( $reqeust->$name_of_facility_permission )) ? $request->$name_of_facility_permission : "writer";

$btn_search_facilities = "component_" . $name . "_btn_search_facilities";
$prepend_facility_area = "component_" . $name . "_prepend_facility_area";
$array_of_facility_ids = "component_" . $name . "_array_facility_ids";

$facility_search_form        = "component_" . $name . "_facility_search_form";
$facility_search_form_opener = "component_" . $name . "_facility_search_form_opener";

$permissions = [ 'owner' => '設備管理者', 'writer' => '設備 予約可', 'reader' => '設備 予約状況閲覧可' ];

$facility_check_toggler = "component_" . $name . "_facility_check_toggler";

//　大分類を検索
//
$ids = [];
if( $value_of_facility_permission  == "owner" ) {
    $ids = Facility::getOwner( user_id() )->pluck('id')->toArray();
} elseif( $value_of_facility_permission  == "writer" ) {
    $ids = Facility::getCanWrite( user_id() )->pluck('id')->toArray();
} elseif( $value_of_faciility_permisson == "reader" )  {
    $ids = Facility::getCanRead( user_id() )->pluck('id')->toArray();
}
$query = DB::table( 'facilities' )->select( 'category, sub_category' );

if( count( $ids ) >= 1 ) {
    $query = $query->whereIn( 'id', $ids );
}
if( count( $facilities ) >= 1 ) {
    $query = $query->orWhere( function( $sub_query ) use ( $facilities ) {
                    $sub_query->whereIn( 'id', $facilities );
                });
}

$query = $query->select( 'category', 'sub_category' )->groupBy( 'category', 'sub_category' );
$categories = $query->get();

#if_debug( $value_of_facility_permission , $ids, $query->get() );

$name_of_categories = "component_" . $name . "_categories";
$request_categories = ( is_array( $request->$name_of_categories )) ? $request->$name_of_categories : [];

@endphp
<div>
    <div class="col-12 m-2">
        <div id="{{ $prepend_facility_area }}">{{ $prepend_facility_area }}</div>
        
        <div class="btn btn-sm btn-outline-dark" id="{{ $facility_check_toggler }}">全てチェックする</div>
        <div class="btn btn_icon" id="{{ $facility_search_form_opener }}">@icon( caret-square-down )</div>

        <div id="{{ $facility_search_form }}">
            {{--
            <label for="{{ $name_of_show_hidden_facilities }}">非表示設備も検索</label>
            {{ Form::checkbox( $name_of_show_hidden_facilities, 1, $request->$name_of_show_hidden_facilities , [ 'id' => $name_of_show_hidden_facilities , 'class' => 'checkboxradio' ] ) }}
            --}}
            {{ Form::select( $name_of_facility_permission, $permissions, $value_of_facility_permission, [ 'id' => $name_of_facility_permission, 'class' => 'form-control' ] ) }}

            @foreach( $categories as $category )
                @if( $loop->first ) <hr><div class="container"><div class="row"> @endif
                @php
                $id = $name_of_categories . "_" . $category->category;
                $checked = ( in_array( $category->category, $request_categories )) ? 1 : 0;
                @endphp
            
                <label for="{{ $id }}">{{ $category->category }}</label>
                {{ Form::checkbox( $name_of_categories."[]", $category->category , $checked , [ 'id' => $id , 'class' => "checkboxradio $name_of_categories" ] ) }}

                @if( $loop->last ) </div></div> @endif
            @endforeach
            
            <div class="btn btn-outline-dark m-1" id="{{ $btn_search_facilities }}">設備を再検索</div>
        </div>
    </div>
    
    <script>
        //　選択され設備ID（ facility_id の配列）
        //
        let {{ $array_of_facility_ids }} = [{{ implode( ",", $facilities ) }}];
        
        console.log( {{ $array_of_facility_ids }} );
        
        $("#{{ $facility_search_form_opener }}").on( 'click', function() {
            $("#{{ $facility_search_form }}").toggle( 'blind', { percent: 20 }, 200 );
        });
        
        //　設備検索AJAX
        //
        $('#{{ $btn_search_facilities }}').on( 'click', function() {

            //　チェックされている設備IDを更新
            //
            update_array_of_facility_ids();

            //　設備を検索し、設備リストを更新する
            //
            search_facilities();        
        });
        
        function search_facilities() {

            //　設備の検索条件
            //
            if( $('#{{ $name_of_show_hidden_facilities }}').prop( 'checked' )) {
                var show_hidden_facilities = 1;
            } else {
                var show_hidden_facilities = 0;
            }
            var facility_permission   = $('#{{ $name_of_facility_permission }}').val();
            
            //　選択している設備
            //
            $('.{{ $name }}').each( function() {
                if( $(this).prop( 'checked' )) {
                    {{ $array_of_facility_ids }}.push( $(this).val() )
                }
            });
            
            //　大分類
            //            
            var categories = [];
            $('.{{ $name_of_categories }}' ).each( function() {
                if( $(this).prop( 'checked' )) {
                    categories.push( $(this).val() )
                }
            });
            
            //　設備を検索
            //
            $.ajax( {
                url : "{{ route( 'ajax.facility.search' ) }}",
                type: "GET",
                data: { show_hidden_facilities : show_hidden_facilities,
                        facility_permission   : facility_permission,
                        facilities             : {{ $array_of_facility_ids }},
                        categories : categories,
                        user_id : {{ user_id() }}
                        }
            
            }).done( function( data, status, xhr ) {
                console.log( data, status );
                update_facilities( data );
            
            }).fail( function( xhr, status, error ) {
            
            });
        }

        //　設備チェックボックスを検索結果に基づき作画する
        //
        function update_facilities( data ) {
            var prepend_facility_area = $("#{{ $prepend_facility_area }}");
            
            var html = ""
            data.forEach( function( facility, i ) {
                
                var id   = "{{ $name }}_facility_id_" + facility['id'];
                var name = "";
                var title = "";
                if( facility['type'] == 'company-wide' ) {
                    name  = facility['name'] + " (全社)";
                } else {
                    name = facility['name'];
                }
                if( facility['sub_category'] ) {
                    title = facility['name'] + "（" + facility['category'] + " " + facility['sub_category'] + "）"; 
                } else {
                    title = facility['name'] + "（" + facility['category'] + "）"; 
                }
                
                html += "<div class='d-flex' style='background-color: " + facility['background_color'] + "; color: " + facility['text_color'] + ";'>";
                html += '<input name="{{ $name }}[]" type="checkbox" class="m-1 {{ $name }}" id="' + id + '" value=' + facility['id'] + '>';
                html += '<label for="' + id + '" style="cursor: pointer" class="m-1 text-truncate" title="' + title + '">' + name + '</label>'; 
                html += '<a class="btn btn-sm" href="{{ url( 'groupware/facility/show' ) }}/' + facility['id'] + '"><i class="fas fa-cog"></i></a>';
                html += "</div>";
            });
            prepend_facility_area.html( '' );
            prepend_facility_area.html( html );
            check_facility_checkboxs();
        }
        
        //　元々チェックされた設備を設備リスト更新後に改めてチェックする
        //
        function check_facility_checkboxs() {
            $(".{{ $name }}").each( function() {
                console.log( {{ $array_of_facility_ids }}, $(this).val(),  {{ $array_of_facility_ids }}.includes( Number( $(this).val() ))  );

                if( {{ $array_of_facility_ids }}.includes( Number( $(this).val() ) ) ){
                    $(this).prop( 'checked', true );
                }
            });
        }
        
        //　チェックボックス選択・解除ボタン
        //
        $('#{{ $facility_check_toggler }}').on( 'click', function() {
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
        
        //　チェックされている設備IDを配列へ格納する
        //
        function update_array_of_facility_ids() {
            {{ $array_of_facility_ids }} = [];
            $(".{{ $name }}").each( function() {
                console.log( {{ $array_of_facility_ids }}, $(this).val(), $(this).prop('checked' ));

                if( $(this).prop('checked' )){
                    {{ $array_of_facility_ids }}.push( Number( $(this).val() ));
                }
            });
        }
        
        
        $(document).ready( function() {
            $('#{{ $facility_search_form }}').hide();
            search_facilities();
        });
        
    </script>
</div>