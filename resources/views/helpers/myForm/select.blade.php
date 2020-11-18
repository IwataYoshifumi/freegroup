@php

use Illuminate\Support\HtmlString;


foreach( $columns as $column ) { 
    if( ! array_key_exists( $column, $columns_name )) {
        $columns_name[$column] = $column;
    }
}

@endphp


<div class="container w-100 bg-light border border-dark p-1">

    <div class="container d-none d-{{$bk}}-block">
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
                <div class="col-12 col-{{$bk}}">
                    <div class="row container">
                        @foreach( $form as $name => $form_options ) 
                            @php
                                #dd( $form );
                                $form_name   = $form_options['form_name'];
                                $id_name     = $form_options['option_column'];
                                $id          = $row[$id_name];
                                if( isset( $form_options['class'] )) { $class = $form_options['class']; } else { $class = "m-1"; }
                                #dd( $route_name, $with, $id_name, $id, $row );
                            @endphp
                        
                                <input type="checkbox" name='{{ $form_name }}[{{ $i }}]' value="{{ $id }}" class="{{ $class }}" id="{{ $form_name }}{{$i}}">
                                <div class="mr-2" for="{{ $form_name }}{{$i}}">{{ $name }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @foreach( $columns as $column ) 
                @if( in_array( $column, $show, true )) 
                    <div class="col-3 d-{{$bk}}-none">{{ $columns_name[$column] }}</div>
                    <div class="col-9 col-{{$bk}}">{{ $row->$column }}</div>
                @endif
            @endforeach
            <div class="col-11 d-{{$bk}}-none border border-secondary container mt-1 mb-1"></div>
        </div>
    @endforeach
</div>

@php
    #dump( $rows );
@endphp