@php
use App\Http\Helpers\ScreenSize;


@endphp

@once
    <!-- 設備予約モーダルウインドウ -->
    
    <div id="modal_window_to_create_reservation" class="my_dialog draggable" title="設備予約">
        <div class="w-100 border border-secondary bg-light"  id="modal_window_to_create_reservation_header">
            <a class="btn" onClick="close_reserve_dialog();">@icon( window-close )</a>
            <span id='title_of_detail_modal'>設備予約</span>
        </div>

        <iframe id='iframe_to_create_reservation' class="bg-white" style="width: 100%; height: 100%" src="">
        </iframe>
        
        {{--
        <div class="w-100 bg-light border border-secondary d-flex justify-content-end" id="modal_window_to_create_reservation_footer" style="position: relative; top: -7px;">
            <a class="btn btn-secondary btn-sm btn-border-dark text-white m-2" onClick="close_reserve_dialog();">閉じる</a>
        </div>
        --}}
    </div>
    
    <script>
        var modal_window_create_reservation = $('#modal_window_to_create_reservation');
        var iframe_to_create_reservation    = $('#iframe_to_create_reservation');
        var header_for_reservation          = $('#modal_window_to_create_reservation_header');
        var footer_for_reservation          = $('#modal_window_to_create_reservation_footer');
    
        @if( ScreenSize::isMobile() )
            var h = screen.availHeight - header_for_reservation.height() - 4;
            var w = screen.availWidth;
            modal_window_create_reservation.width( w );
            modal_window_create_reservation.height( h );
            console.log( 'modal_size', h,w, header_for_reservation.height(), footer_for_reservation.height() );
        @else
            
            var h = screen.availHeight * 2 / 3;
            modal_window_create_reservation.height( h );
        
            var t = ( screen.availHeight - modal_window_create_reservation.height() ) / 3;
            var l = ( screen.availWidth  - modal_window_create_reservation.width()  ) / 2;
            modal_window_create_reservation.css( 'top',  t );
            modal_window_create_reservation.css( 'left', l );
        @endif
    
        modal_window_create_reservation.hide();

        //　新規　設備予約ダイヤログを閉じる
        //
        function close_reserve_dialog() {
            console.log( 'close' );
            var options = { percent: 50 };
            modal_window_create_reservation.hide( 'puff', options , 150 );
            // location.reload();
            $('#search_form').submit();
        }


        $('.click_to_open_modal_create_reservation').on( 'click', function() {
            open_dialog_to_create_reservation( $(this).data('date') );
        });
        
        //　新規　設備予約ダイヤログを表示する
        //
        function open_dialog_to_create_reservation( date ) {
            // var date = "";
            var url = "";
            var title = "";
            var facilities = [];

            // console.log( 'open_dialog_to_create_reservation');
            // date = object.data('date');
    
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
            
            // console.log( date,  facilities, url );
            
            iframe_to_create_reservation.attr( 'src', url );
            $('#loading').show();
        }
        
        iframe_to_create_reservation.on( 'load', function() {
            $('#loading').hide();
            var options = { percent: 50 }
            @if( ScreenSize::isMobile() )
               $(window).scrollTop( -50 );
            @endif
            
            var D = iframe_to_create_reservation.get(0).contentWindow;
            
            var D = $(this).get(0).contentWindow;
			console.log(
                $(this),
			    D,
			    D.document.body.offsetHeight, 
			    D.document.body.scrollHeight,
			    D.document.body.clientHeight,
			    D.document.documentElement.offsetHeight, 
			    D.document.documentElement.scrollHeight,
			    D.document.documentElement.clientHeight,
				);
            
            modal_window_create_reservation.show( 'puff', options , 150 );
        });
        
    </script>
@endonce