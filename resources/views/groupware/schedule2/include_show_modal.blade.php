@once
    <!-- スケジュール詳細表示モーダルウインドウ -->
    <div id="detail_schedule_dialog" class="iframe_seamless" title="スケジュール詳細">
        <iframe id='detail_schedule_iframe' src="" width=670 height=360>
        </iframe>
    </div>
    <script>
        $( function() {
           $('#detail_schedule_dialog').dialog( { 
                autoOpen: false,
                miniHeight: 480,
                maxHeight:780,
                minWidth: 710,
                maxWidth: 860,
                closeOnEscape: true,
                buttons: [ { text: '閉じる', click: function() { $(this).dialog( 'close' ); }} ]
            });
        });
        $('.schedule_item').on( 'click', function() {
            var schedule_id = $(this).data( 'schedule_id' );
            var url  = "{{ url( '/groupware/schedule/show_modal/' ) }}";
            url += "/" + schedule_id;
            console.log( schedule_id );
            
            $('#detail_schedule_iframe').attr( 'src', url );
            $('#detail_schedule_dialog').dialog( 'open' );
        });
        
    </script>
@endonce