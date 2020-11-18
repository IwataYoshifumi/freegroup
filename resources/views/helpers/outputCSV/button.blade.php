@php

use Illuminate\Support\HtmlString;

// 引数
//
// $inputs 　　　検索クエリー
// $route_name　ルート名
// $method　　フォームのメソッド
// $class     ボタンのクラス

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

@endphp

{{ Form::open( [ 'route' => $route_name, 'method' => $method ] ) }}
    @csrf
    @foreach( $inputs as $name => $value )
        @if( ! is_array( $value )) 
            {{ Form::hidden( $name, $value ) }}
        @else 
            {{ new HtmlString( hidden_forms( $name, $value )) }} 
        @endif
    @endforeach

    <button class="btn btn-warning m-1">CSV出力</button>
{{ Form::close() }}