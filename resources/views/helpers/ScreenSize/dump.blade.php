@extends('layouts.app')

@php
$screen_size = session( 'ScreenSize' );

@endphp

@section('content')

    <h4>ScreenSize.Dump Page</h4><br>
    
    @if( is_array( $screen_size )) 
        width : {{ $screen_size['width'] }}<br>
        height: {{ $screen_size['height'] }}<br>
        update: {{ $screen_size['updated_at'] }}<br>
        @php
        dump( session()->all() );
        @endphp
    
    @endif
    


@endsection