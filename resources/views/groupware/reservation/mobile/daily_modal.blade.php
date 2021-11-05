<!--
  --
  -- 日次表示ダイヤログ
  --
  -->
<div id="daily_modal" class="draggable card shadow-lg shadow-dark" style="position: absolute; top: 10%; height: 80%; left: 10%; width:80%; z-index: 200;">
    <div class="card-head bg-light" style="pointer-event: none;">
        <span class="btn " onClick="hide_daily_modal();">@icon( window-close )</span>
        <span class="text-center w-100" style="font-size: small;">日次表示</span>
    </div>
    <iframe id="iframe_for_daily_modal" style="width: 100%; height:100%" >
    </iframe>
</div>

<!--<div class="loading">@icon( loading )</div>-->

<script>
    var daily_modal = $('#daily_modal');
    var loading     = $('#loading');
    daily_modal.hide();
    
    // daily_modal.hide();

    // iFrame の高さ調整スクリプトをロード
    //
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = "/js/fit_ifr.js?auto=0"; 
    document.head.appendChild(script);

    //　日次表示のURL
    //
    @php
    $loop = 0;
    #$requested_url = "";
    // $requested_url = route( 'groupware.show_all.daily' ) . "?";
    $requested_url = route( 'groupware.reservation.dialog.daily' ) . "?";
    foreach( request()->all() as $key => $values ) {
        if( $key == "base_date" ) { continue; }

        if( is_array( $values )) {
            foreach( $values as $i => $value ) {
                if( $loop ) { $requested_url .= "&"; }
                $requested_url .= $key . '[]=' . $value;
            }
        } else {
            if( $loop ) { $requested_url .= "&"; }

            $requested_url .= $key . "=" . $values;
        }
        $loop++;
    }
    #$requested_url = route( 'groupware.show_all.daily' ) . "?" . $requested_url;
    @endphp
    var requested_url = '{!! $requested_url !!}'; {{-- htmlspecialchars OK --}} 
    var iframe = $('#iframe_for_daily_modal');
    
    // 日付ボックスをクリックして、日次詳細ダイヤログを表示
    //
    $('.date_box').on( 'click', function() {
        show_daily_daialog( $(this) );        
        // var d = $(this).data('date');
        // var url = requested_url + "&base_date=" + d;
        // iframe.attr( 'src', url );
        // $('#loading').show();
        // console.log( d, url );
    });

    // 日次詳細ダイヤログを表示
    //
    function show_daily_daialog( object ) {
        var d = object.data('date');
        var url = requested_url + "&base_date=" + d;
        iframe.attr( 'src', url );
        $('#loading').show();
        console.log( d, url );
    }

    // iframeの高さ調整
    //
    iframe.on( 'load', function() {
        var options = { percent: 50 };
        $('#loading').hide();
        daily_modal.show( 'puff', options , 200 );
        // fitIfr();
    });

    // 日次モーダルウインドウを閉じる
    //
    function hide_daily_modal() {
        var options = { percent: 50 };
        daily_modal.hide( 'puff', options , 200 );
        daily_modal.height( '0%' );
    }

</script>

