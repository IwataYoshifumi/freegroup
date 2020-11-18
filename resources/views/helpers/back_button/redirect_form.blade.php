@php

use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Requests;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

function hidden_forms( $NAME, $VALUE ) {
    $return = "";
    foreach( $VALUE as $key => $v ) {
        $name = $NAME."[".$key."]";
        if( ! is_array( $v )) {
            $return .= Form::hidden( $name, $v );
            $return .= "\n";
        } else {
            $return .= hidden_forms( $name, $v );
        }
    }
    return $return;
}

$wait = 0;

$route_name = $session['route_name'];
$url        = $session['url'];
$method     = $session['method'];
$para = [];
if( isset( $session['para'] )) {
    $para = $session['para'];
} 
@endphp
<html>
    <body>    
        {{ Form::open( [ 'url' => $url, 'method' => $method, 'id' => 'back_form' ] ) }}
            @csrf
                @foreach( $para as $name => $value )
                    @if( ! is_array( $value )) 
                        {{ Form::hidden( $name, $value ) }}
                    @else 
                        {{ new HtmlString( hidden_forms( $name, $value )) }} 
                    @endif
                @endforeach

                @if( $wait )
                    <pre>
                        url :  {{ $url }}
                        methos : {{ $method }}
                   </pre>
                <button type="submit">戻るボタン</button>
                @endif
                
        {{ Form::close() }}

        
        
        戻ります・・・
        <script type="text/javascript"> 
            setTimeout( function() {
                document.getElementById("back_form").submit();
            }, {{ $wait }} );
        </script>
    </body>
</html>

