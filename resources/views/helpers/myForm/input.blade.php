@php

use Illuminate\Support\HtmlString;

@endphp

<div class="container w-100 bg-light border border-dark p-1">
    @foreach( $names as $name ) 
        @php

            $type       = ( ! empty( $types[$name]      )) ? $types[$name]        : "text";
            $label      = ( isset( $labels[$name]       )) ? $labels[$name]       : $name;
            $default    = ( isset( $defaults[$name]     )) ? $defaults[$name]     : null ;
            $confirm    = ( isset( $confirms[$name]     )) ? $confirms[$name]     : false ;
            $form_class = ( isset( $form_classes[$name] )) ? $form_classes[$name] : "col-12" ;

            $div_label_class = "m-1 col-12 col-$bk-4 text-$bk-right";
            $div_form_class  = "m-1 col-12 col-$bk-7 ";
            $options = [ 'class' => "$form_class" ];
            
            
        @endphp
        <div class="container-fluid">
        <!--<div class="container">-->
            <!--<div class="row no-gutters">-->
            <div class="row">

                @php
                    if( $type == "password" ) {
                        $old = "";
                    } else {
                        $old = old( "input[$name]", $default );
                    }
                @endphp

                @switch( $type )
                    @case( "text" )
                    @case( "password" )
                    @case( "number" )
                    @case( "email" )
                    @case( "date" )
                    @case( "textarea" )
                        @if( ! $confirm )   
                            <label class="{{ $div_label_class }} bg-warning">{{ $label }}</label>
                            <div class="{{ $div_form_class }} bg-info">
                                {{ Form::$type( "input[$name]", $old, $options ) }}
                                <!--<input type="text" name="aaa[{{ $name }}]" value="{{ old( "aaa[$name]" ) }}">-->
                            </div>
                        @else 
                            <label class="{{ $div_label_class }} bg-warning">{{ $label }}<br><div class="m-1 d-none d-{{$bk}}-block">（確認）</div></label>
                            <div class="{{ $div_form_class }} bg-info">
                                {{ Form::$type( "input[$name]",                  null, $options ) }}<div class="col-12"></div>
                                {{ Form::$type( "input[".$name."_confirmation]", null, $options ) }}
                            </div>
                        @endif
                        @break

                    @case( "select" )
                        @php 
                            $array   = ( isset( $values[$name]   )) ? array_merge( [ "" => "" ], $values[$name] ) : [] ;
                        @endphp
                        <label class="{{ $div_label_class }} bg-warning">{{ $label }}</label>
                        <div class="{{ $div_form_class }} bg-info">
                            {{ Form::select( "input[$name]", $array, $old, $options ) }}
                        </div>
                        @break

                    @case( "radio" )
                        <label class="{{ $div_label_class }} bg-warning">{{ $label }}</label>
                        <div class="{{ $div_form_class }} bg-info">
                            @foreach( $values[$name] as $key => $value )
                                @php
                                    if( is_numeric( $key )) { $key = $value; }
                                @endphp
                                <div>{{ Form::radio( "input[$name]", $value ) }} {{ $key }}</div>
                            @endforeach
                        </div>
                        @break

                    @case( "checkbox" )
                    @case( "checkboxes" )
                        <label class="{{ $div_label_class }} bg-warning">{{ $label }}</label>
                        <div class="{{ $div_form_class }} bg-info float-left">
                            <div class="row">
                            @foreach( $values[$name] as $key => $value )
                                @if( isset( $value ))
                                    @php
                                        $old = old( "input[$name][$key]", ( isset( $default[$key] )) ? $default[$key] : null );
                                        if( is_numeric( $key )) { $key = $value; }
                                        #dump( $defaults,$old, $checked );
                                    @endphp
                                    <div class="col-4">
                                        <div class="row p-1">
                                            {{ Form::checkbox( "input[$name][$key]", $value, "",  [ 'class' => "col-2 align-middle" ] ) }}
                                            <div class="col-9">{{ $key }}</div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            </div>
                        </div>
                    
                        @break
                    @case( "hidden" )
                        {{ Form::hidden( "input[$name]", $default ) }}
                        
                        @break
                    @default
                        <div class="{{ $form_class }} text-danger bg-warning">
                            未定義フォーム「{{ $name }}」フォームタイプ「{{ $type }}」
                        </div>
                        @break
                @endswitch
            </div>
        </div>
    
    @endforeach
</div>

@php
    dump( [ 'types' => $types, 'names' => $names, 'values' => $values, ] );
    dump( Request::all(), Request::session() );
@endphp