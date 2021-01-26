<!-- カレンダー表示切替ボタン -->
<button id="switch_calendars_display_dialog_opener" class="btn btn-sm btn-outline-dark m-1">表示カレンダー</button>

@once    
    <!-- カレンダー表示切替ダイアログ -->
    <div id="switch_calendars_display_dialog" title="カレンダー表示切替">
        @foreach( $Calprops as $calprop )
            @if( $loop->first )
            <div class="row m-2 bg-light">
                <div class="col-3">表示切替</div>
                <div class="col-8">カレンダー名</div>            
            @endif
            @php
                $id = "calprop_" . $calprop->id;
            @endphp
                <div class="col-12 mb-1"></div>
                <label class="col-3" for="{{ $id }}">表示</label>
                {{ Form::checkbox( $id, $calprop->calendar_id, true, [ 'class' => 'checkboxradio switch_calendar_display_btn', 'id' => $id ] ) }}
                <div class="col-8" style="{{ $calprop->style() }}">{{ $calprop->name }}</div>
            @if( $loop->last )
            </div>        
            @endif
        @endforeach
    </div>
    
    <!-- カレンダー表示切替スクリプト -->
    <script>
        $( function() {
           $('#switch_calendars_display_dialog').dialog( { 
                autoOpen: false,
                miniHeight: 580,
                minWidth: 480,
                closeOnEscape: true,
                buttons: [ { text: '閉じる', click: function() { $(this).dialog( 'close' ); }} ]
            });
        });
    
        /*
         * カレンダー表示切替ダイヤログの表示
         */
        $('#switch_calendars_display_dialog_opener').on( 'click', function() {
           $('#switch_calendars_display_dialog').dialog( 'open' );
        });
        
        /*
         *
         * カレンダーの表示切替ボタンの動作
         *
         */
        $('.switch_calendar_display_btn').on( 'click', function() {
            let calendar_id = $(this).val();
            let checked     = $(this).prop( 'checked' );
            let selector    = "calendar_" + calendar_id;
            //console.log( calendar_id, checked, selector );
    
            if( checked ) {
                $( "." + selector ).each( function() {
                    //$(this).css( 'visibility', 'visible' );
                    //$(this).css( 'display', 'block' );
                    $(this).show( 300 );
                    
                    console.log( 'display', $(this).css( 'display' ),$(this).css( 'visibility'),$(this).data('calendar_id'));
                });
            } else {
                $( "." + selector ).each( function() {
                    //$(this).css( 'visibility', 'hidden' );
                    //$(this).css( 'dispaly', 'none' );
                    $(this).hide( 300 );
                    console.log( 'none', $(this).css( 'display' ), $(this).css('visibility' ), $(this).data('calendar_id') );
                });
            }
        });
        
    </script> 
@endonce