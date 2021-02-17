@php

use App\myHttp\GroupWare\Models\Calendar;

$calendar_types = Calendar::getTypes();



@endphp

<!-- カレンダー表示切替ボタン -->
<button id="switch_calendars_display_dialog_opener" class="btn btn-sm btn-outline-dark m-1">表示カレンダー</button>

@once    
    <!-- カレンダー表示切替ダイアログ -->
    <div id="switch_calendars_display_dialog" title="カレンダー表示切替">
        @foreach( $Calprops as $calprop )
            @if( $loop->first )
            <div class="row m-2 bg-light">
                <div class="col-2">表示切替</div>
                <div class="col-4">カレンダー名</div>
                <div class="col-2">色設定</div>
                <div class="col-3">種別</div>
            @endif
            @php
                $id = "calprop_" . $calprop->id;
                $calendar = $Calendars->find( $calprop->calendar_id );
                $calendar_type = $calendar_types[ $calendar->type ];

                $route_to_calprop = route( 'groupware.calprop.update', ['calprop' => $calprop->id] );

            @endphp
                <div class="col-12 mb-1"></div>
                <label class="col-2" for="{{ $id }}">表示</label>
                {{ Form::checkbox( $id, $calprop->calendar_id, true, [ 'class' => 'checkboxradio switch_calendar_display_btn btn-sm m-1', 'id' => $id ] ) }}
                <div class="col-4" style="{{ $calprop->style() }}">{{ $calprop->name }}</div>
                <a class="col-2 btn btn_icon m-1" href="{{ $route_to_calprop }}" target="_parent"> @icon( edit ) </a>
                <div class="col-3">{{  $calendar_type }}</div>

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
                minWidth: 640,
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