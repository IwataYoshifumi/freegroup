@once
    <!-- スケジュール・タスク詳細表示モーダルウインドウ -->

    <div id="detail_modal_dialog" class="iframe_seamless" title="詳細表示">
        <iframe id='detail_modal_iframe' src="" width=670 height=360>
        </iframe>
    </div>

    <script>
        var detail_modal_dialog = $('#detail_modal_dialog');
        var detail_modal_iframe = $('#detail_modal_iframe');
    
        $( function() {
           detail_modal_dialog.dialog( { 
                autoOpen: false,
                miniHeight: 480,
                maxHeight:780,
                minWidth: 710,
                maxWidth: 860,
                closeOnEscape: true,
                buttons: [ { text: '閉じる', click: function() { $(this).dialog( 'close' ); }} ]
            });
        });

        $('.show_modal_detail_object').on( 'click', function() {
            var object_type = $(this).data('object_type');
            var object_id   = $(this).data('object_id'  );

            if( object_type == "schedule" ) {
                var title = "スケジュール詳細";
                var url  = "{{ url( '/groupware/schedule/show_modal/' ) }}";
                url += "/" + object_id;
            
            } else if( object_type == "task" ) {
                var title = "タスク詳細";
                var url  = "{{ url( '/groupware/task/show_modal/' ) }}";
                url += "/" + object_id;
            
            } else if( object_type == "report" ) {
                var title = "日報詳細";
                var url  = "{{ url( '/groupware/report/show_modal/' ) }}";
                url += "/" + object_id;
            }            
        
            detail_modal_iframe.attr( 'src', url );
            detail_modal_dialog.dialog( { title: title } );
            detail_modal_dialog.dialog( 'open' );
        });


        
    </script>
@endonce