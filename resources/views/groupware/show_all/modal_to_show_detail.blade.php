@once
    <!-- スケジュール・タスク詳細表示モーダルウインドウ -->

    <div id="modal_window_to_show_detail" class="iframe_seamless" title="詳細表示">
        <iframe id='iframe_to_show_detail' src="" width=670 height=360>
        </iframe>
    </div>

    <script>
        var modal_window          = $('#modal_window_to_show_detail');
        var iframe_to_show_detail = $('#iframe_to_show_detail');
    
        $( function() {
           modal_window.dialog( { 
                autoOpen: false,
                miniHeight: 480,
                maxHeight:780,
                minWidth: 710,
                maxWidth: 860,
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
            }
            iframe_to_show_detail.attr( 'src', url );
            modal_window.dialog( { title: title } );
            modal_window.dialog( 'open' );
            
        });
    </script>
@endonce