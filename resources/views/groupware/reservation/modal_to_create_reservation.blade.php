@once
    <!-- 設備予約モーダルウインドウ -->
    
    <div id="modal_window_to_create_reservation" class="iframe_seamless" title="設備予約">
        <iframe id='iframe_to_create_reservation' src="" width=670 height=480>
        </iframe>
    </div>
    
    <script>
        var modal_window_create_reservation = $('#modal_window_to_create_reservation');
        var iframe_to_create_reservation    = $('#iframe_to_create_reservation');
    
        $( function() {
           modal_window_create_reservation.dialog( { 
                autoOpen: false,
                miniHeight: 600,
                maxHeight:980,
                minWidth: 710,
                maxWidth: 860,
                closeOnEscape: true,
                buttons: [ { text: '閉じる', click: function() { close_reserve_dialog(); }} ]
            });
        });
        
        function close_reserve_dialog() {
            modal_window_create_reservation.dialog( 'close' );
            location.reload();
        }
    
        
        $('.click_to_open_modal_create_reservation').on( 'click', function() {
            var date = $(this).data('date');
            var url = "";
            var title = "";
    
            var facilities = [];
            $('.facilities').each( function() {
                if( $(this).prop( 'checked' )) {
                    facilities.push( $(this).val() );
                } 
            });
    
            //url  = "/groupware/reservation/create_modal/";
            url  = "{{ url('/groupware/reservation/create_modal/' ) }}";
            url += '?base_date=' + date;
            facilities.forEach( function( value, index ) {
                url += "&facilities[" + index + "]=" + value;    
            });
            
            
            title = "設備予約";
            
            console.log( date,  facilities, url );
            
            iframe_to_create_reservation.attr( 'src', url );
            modal_window_create_reservation.dialog( { title: title } );
            modal_window_create_reservation.dialog( 'open' );
            
        });
    </script>
@endonce