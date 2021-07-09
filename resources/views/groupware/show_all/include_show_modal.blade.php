@once
    <!-- スケジュール・タスク詳細表示モーダルウインドウ -->

    <div id="detail_dialog" class="iframe_seamless" title="詳細表示">
        <iframe id='detail_iframe' src="" width=670 height=360>
        </iframe>
    </div>

    <script>
        var detail_dialog = $('#detail_dialog');
        var detail_iframe = $('#detail_iframe');
    
        $( function() {
           detail_dialog.dialog( { 
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
            
            detail_iframe.attr( 'src', url );
            detail_dialog.dialog( { title: "スケジュール詳細表示" } );
            detail_dialog.dialog( 'open' );
            
        });

        $('.task_item').on( 'click', function() {
            var task_id = $(this).data( 'task_id' );
            var url  = "{{ url( '/groupware/task/show_modal/' ) }}";
            url += "/" + task_id;
            console.log( task_id );
            
            detail_iframe.attr( 'src', url );
            detail_dialog.dialog( { title: "タスク詳細表示" } );
            detail_dialog.dialog( 'open' );
        });

        /*
        $('.date_item').on( 'click', function() {
            var date = $(this).data( 'date' );
            // var url  = "{{ url( '/groupware/schedule/daily/' ) }}";
            var url  = "{{ url( '/groupware/show_all/daily/' ) }}";
            url += "/?base_date=" + date;
            console.log( date );
            
            detail_iframe.attr( 'src', url );
            detail_dialog.dialog( { title: date + " の予定・タスク " } );
            detail_dialog.dialog( 'open' );
        });
        */
        $('.date_item').on( 'click', function() {
            var date = $(this).data( 'date' );
            var url  = "{{ url( '/groupware/show_all/daily/' ) }}";
            // var url  = "{{ url( '/groupware/show_all/dialog/daily/' ) }}";
            $('#base_date').val( date );
            $('#search_form').attr( 'action', url );
            $('#search_form').submit();
            
        });

        
    </script>
@endonce