@php
$dialog_id = "switch_display_schedule_by_user";
$dialog_opener_id = $dialog_id . "_opener";

$user_display_btn_class = "switch_display_schedule_button_by_user";
$dept_display_btn_class = "switch_display_schedule_button_by_dept";

@endphp

<!-- 社員表示切替ボタン -->
<button id="{{ $dialog_opener_id }}" class="btn btn-sm btn-outline-dark m-1">社員表示切替</button>

@once    
    <!-- 社員表示切替ダイアログ -->
    <div id="{{ $dialog_id }}" title="社員表示切替">
        
        @foreach( $Depts as $dept )
            @if( $loop->first ) <div class="row w-95 bg-light">  @endif
                @php
                $dept_id = "dept_" . $dept->id;
                @endphp
                
                <div class="col-11 m-1">
                    <h5>{{ $dept->name }}
                        <div class="btn btn-sm btn-outline-dark {{ $dept_display_btn_class }}" id='{{ $dept_id }}' data-show=1 data-dept_id='{{ $dept->id }}'>表示・非表示</div>
                    </h5>
                </div>

                @foreach( $Users as $user )
                    @if( $user->dept_id != $dept->id ) @continue @endif
                    @php
                        $id = "user_" . $user->id;
                    @endphp
                    <label class="col-2" for="{{ $id }}">{{ $user->name }}</label>
                    {{ Form::checkbox( $id, $user->id, true, [ 'class' => "checkboxradio $user_display_btn_class", 'id' => $id, 'data-dept_id' => $dept->id ] ) }}
                @endforeach
                <div class="col-12 m-2"></div>
            @if( $loop->last ) </div>  @endif
        @endforeach
    </div>
    
    <!-- 社員表示切替スクリプト -->
    <script>
        $('.{{ $dept_display_btn_class }}').on( 'click', function() {
            var show    = $(this).data('show');
            var dept_id = $(this).data('dept_id');
            if( show == 1 ) { show = 0; } else { show = 1; }
            $(this).data( 'show', show );
            
            $('.{{ $user_display_btn_class }}[data-dept_id=' + dept_id + ']').each( function() {
                if( show == 1 ) { 
                    if( ! $(this).prop( 'checked' ) ) { $(this).click(); }
                } else {
                    if(   $(this).prop( 'checked' ) ) { $(this).click(); }
                }
            });
            
            
        });
    
    
        $( function() {
           $('#{{ $dialog_id }}').dialog( { 
                autoOpen: false,
                miniHeight: 580,
                minWidth: 1000,
                closeOnEscape: true,
                buttons: [ { text: '閉じる', click: function() { $(this).dialog( 'close' ); }} ]
            });
        });
    
        /*
         * 社員表示切替ダイヤログの表示
         */
        $('#{{ $dialog_opener_id }}').on( 'click', function() { $('#{{ $dialog_id }}').dialog( 'open' ); });
        
        /*
         *
         * カレンダーの表示切替ボタンの動作
         *
         */
        $('.{{ $user_display_btn_class }}').on( 'click', function() {
            let user_id   = $(this).val();
            let checked   = $(this).prop( 'checked' );
            let selector  = "user_" + user_id;
            console.log( user_id, checked, selector );
    
            if( checked ) {
                $( "." + selector ).each( function() {
                    //$(this).css( 'visibility', 'visible' );
                    //$(this).css( 'display', 'block' );
                    $(this).show( 300 );
                    //console.log( $(this).css( 'display' ));
                });
            } else {
                $( "." + selector ).each( function() {
                    //$(this).css( 'visibility', 'collapse' );
                    //$(this).css( 'display', 'none' );
                    $(this).hide( 300 );
                    //console.log( $(this).css( 'display' ));

                });
            }
        });
        
    </script> 
@endonce