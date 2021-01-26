@php

use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

use App\Http\Helpers\ScreenSize;

$route_to_set_screen_size = route( 'screensize.set' );
$route_to_back_one = route( 'back_one' );

@endphp
@include( 'layouts.header' )

<div>
    <div id="status">画面サイズを確認しています・・・</div>
    <div id="log"></div>

    <script type="text/javascript">
    
        var url = '{{ $route_to_set_screen_size }}';
        
        var fd = new FormData();
        fd.append( 'width',  $(window).width()   );
        fd.append( 'height', $(window).height()   );
        fd.append( '_token', '{{ csrf_token() }}' );
        console.log( fd );
        
        $.ajax({ 
            url: url,
            method: 'POST',
            dataType: 'json',
            contentType:false,
            processData: false,
            cache: false,
            data: fd,
            
        }).done( function( data, status, xhr ) {
            $("#log").append( status + "<br>");
            console.log( data, status, xhr ); 
            var wait = 500;
            
            setTimeout( function() {
                window.location.href = '{{ $route_to_back_one }}';
            }, wait );
            
        }).fail( function( xhr, status, error ) {
            $("#log").append("xhr.status = " + xhr.status + "<br>");          // 例: 404
            $("#log").append("xhr.statusText = " + xhr.statusText + "<br>");  // 例: Not Found
            $("#log").append("status = " + status + "<br>");                  // 例: error
            $("#log").append("error = " + error + "<br>");
        });

    </script>
</div>