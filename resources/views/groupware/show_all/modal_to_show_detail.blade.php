@php
use App\Http\Helpers\ScreenSize;

@endphp

@once
    <!-- スケジュール・タスク詳細表示モーダルウインドウ -->

    <div id="modal_window_to_show_detail" class="iframe_seamless" title="詳細表示">
        <iframe id='iframe_to_show_detail' src="" width=670 height=360>
        </iframe>
    </div>

    <script>
        var modal_window          = $('#modal_window_to_show_detail');
        var iframe_to_show_detail = $('#iframe_to_show_detail');
        
        var window_w = $(document).width();
        var window_h = $(document).height();
        console.log( 'window', window_w, window_h );
    
        $( function() {
           modal_window.dialog( { 
           
                @php
                $screen_width = ( ScreenSize::getWidth() < ScreenSize::md ) ? ScreenSize::getWidth() : 750 ;
                @endphp
           
                autoOpen: false,
                // height: 780,
                width: {{ $screen_width }},
                // miniHeight: 480,
                // maxHeight:780,
                // minWidth: 710,
                // maxWidth: 860,
                closeOnEscape: true,
                buttons: [ { text: '閉じる', click: function() { $(this).dialog( 'close' ); }} ]
            });
        });

        
        $('.object_to_show_detail').on( 'click', function() {
            var object = $(this).data('object');
            var object_id = $(this).data('object_id');
            var url = "";
            var title = "";
            console.log( object, object_id, $(this).html() );

            if( object == "schedule" ) {
                url += "{{ url( '/groupware/schedule/show_modal/' ) }}";
                url += "/" + object_id;
                title = "スケジュール詳細";
            } else if( object == "task" ) {
                url += "{{ url( '/groupware/task/show_modal/' ) }}";
                url += "/" + object_id;
                title = "タスク詳細";

            } else if( object == "report" ) {
                url += "{{ url( '/groupware/report/show_modal/' ) }}";
                url += "/" + object_id;
                title = "日報詳細";
            } else if( object == "reservation" ) {
                url += "{{ url( '/groupware/reservation/show_modal/' ) }}";
                url += "/" + object_id;
                title = "設備予約詳細";
            }
            
            
            iframe_to_show_detail.attr( 'src', url );
            modal_window.dialog( { title: title } );
            modal_window.dialog( 'open' );
        });

        iframe_to_show_detail.on( 'load', function() {
        
            // var modal_window          = $('#modal_window_to_show_detail');
        
            var w = $(this).prop( 'scrollWidth' );
            var h = $(this).prop( 'scrollHeight' );
            console.log( 'onloaded', w, h );

            // $(this).width( w   );
            // $(this).height( h  );
            
            // modal_window.daialog( { width: w, height: h } );
        });

    </script>
@endonce