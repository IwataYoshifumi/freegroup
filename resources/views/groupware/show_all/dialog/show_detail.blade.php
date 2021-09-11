@php
use App\Http\Helpers\ScreenSize;


@endphp

<!-- スケジュール・タスク詳細表示モーダルウインドウ -->

<div id="modal_window_to_show_detail" class="my_dialog draggable">
    <div class="w-100 border border-secondary bg-light"  id="modal_window_to_show_detail_header">
        <a class="btn" onClick="close_detail_dialog();">@icon( window-close )</a>
        詳細表示
    </div>
    <iframe id='iframe_to_show_detail' src="" style="width: 100%; height: 100%" class="border border-secondary">
    </iframe>
    <div class="w-100 bg-light border border-secondary d-flex justify-content-end" id="modal_window_to_show_detail_footer" style="position: relative; top: -7px;">
        <a class="btn btn-secondary btn-sm btn-border-dark text-white m-2" onClick="close_detail_dialog();">閉じる</a>
    </div>
</div>

<script>
    var modal_window          = $('#modal_window_to_show_detail');
    var iframe_to_show_detail = $('#iframe_to_show_detail');
    var header                = $('#modal_window_to_show_detail_header');
    var footer                = $('#modal_window_to_show_detail_footer');
    
    /*
     *
     * 詳細ダイアログの大きさ、表示位置設定
     *
     */
    @if( ScreenSize::isMobile() )
        var h = screen.availHeight - header.height() - footer.height() - 4;
        var w = screen.availWidth;
        modal_window.width( w );
        modal_window.height( h );
    @else
        var t = ( screen.availHeight - modal_window.height() ) / 3;
        var l = ( screen.availWidth  - modal_window.width()  ) / 2;
        modal_window.css( 'top',  t );
        modal_window.css( 'left', l );
    @endif

    modal_window.hide();

    $('.object_to_show_detail').on( 'click', function() {
        click_object_to_show_detail( $(this ));
    });

    function click_object_to_show_detail( object ) {
        var object_name = $(object).data('object');
        var object_id = $(object).data('object_id');
        var url = "";
        var title = "";
        // console.log( object, object_id, $(this).html() );

        if( object_name == "schedule" ) {
            url += "{{ url( '/groupware/schedule/show_modal/' ) }}";
            url += "/" + object_id;
            title = "スケジュール詳細";
        } else if( object_name == "task" ) {
            url += "{{ url( '/groupware/task/show_modal/' ) }}";
            url += "/" + object_id;
            title = "タスク詳細";

        } else if( object_name == "report" ) {
            url += "{{ url( '/groupware/report/show_modal/' ) }}";
            url += "/" + object_id;
            title = "日報詳細";
        } else if( object_name == "reservation" ) {
            url += "{{ url( '/groupware/reservation/show_modal/' ) }}";
            url += "/" + object_id;
            title = "設備予約詳細";
        }
        
        iframe_to_show_detail.attr( 'src', url );
        $('#loading').show();
    }

    
    function close_detail_dialog() {
        var options = { percent: 50 };
        modal_window.hide( 'puff', options , 150 );
    }

    iframe_to_show_detail.on( 'load', function() {
        $('#loading').hide();
        var options = { percent: 50 }
        modal_window.show( 'puff', options , 150 );
    });

</script>