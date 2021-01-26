@php

use Illuminate\Support\HtmlString;

//if_debug( $rows );


foreach( $columns as $column ) { 
    if( ! array_key_exists( $column, $columns_name )) {
        $columns_name[$column] = $column;
    }
}

@endphp


<div class="container w-100 bg-light border border-dark p-1">

    <div class="container d-none d-{{ $bk }}-block">
        <div class="row">   
            @if( count( $form ) >= 1 ) 
                <div class="col">
                    @foreach( $form as $name => $values ) 
                        {{ $name }}
                    @endforeach
                </div>
            @endif
            @foreach( $columns as $column ) 
                @if( in_array( $column, $show, true ))
                    <div class="col">
                            {{ $columns_name[$column] }}            
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @foreach( $rows as $i => $row ) 

        <div class="row mt-1">
            @if( count( $form ) >= 1 ) 
                <div class="col-12 col-{{ $bk }}">
                    @foreach( $form as $name => $form_options ) 
                        @php
                            #dd( $form );
                            $route_name  = $form_options['route_name'];
                            $option_name = $form_options['option_name'];
                            $id_name     = $form_options['option_column'];
                            $id          = $row[$id_name];
                            if( isset( $form_options['class'] )) { $class = $form_options['class']; } else {  $class = "btn-outline-dark"; }
                            #dd( $route_name, $with, $id_name, $id, $row );
                            $url = route( $route_name, [ $option_name => $id ] );
                        @endphp
                        <a class='btn btn-sm {{ $class }}' href='{{ $url }}'>{{ $name }}</a>
                    @endforeach
                </div>
            @endif

            @foreach( $columns as $column ) 
                @if( in_array( $column, $show, true ))
                    <div class="col-3 d-{{ $bk }}-none">{{ $columns_name[$column] }}</div>
                    <div class="col-9 col-{{ $bk }}  text-truncate">{{ $row->$column }}</div>
                @endif
            @endforeach
            <div class="col-11 d-{{ $bk }}-none border border-secondary container mt-1 mb-1"></div>
        </div>
    @endforeach

</div>

@php
    #if_debug( $rows );
@endphp