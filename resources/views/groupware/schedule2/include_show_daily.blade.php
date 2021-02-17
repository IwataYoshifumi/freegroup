@php

#dd( $request->all() );
$skip_keys = [ '_token', '_method', 'start_date', 'end_date', 'base_date', 'calendar_types' ];
#$form_type = 'text';
$form_type = 'hidden';



@endphp

{{ Form::open( [ 'route' => 'groupware.schedule.daily', 'id' => 'form_to_show_daily', 'method' => 'GET' ] ) }}
    @csrf
    @foreach( $request->all() as $key => $value )
        @if( in_array( $key , $skip_keys )) @continue @endif
        @if( is_array( $value ) and ( $key == 'users' or $key == 'depts' ))
            @foreach( $value as $v )
                <input type='{{ $form_type }}' text name="{{ $key }}[]" value="{{ $v }}" title="{{ $key }}">
            @endforeach
        @else
            <input type='{{ $form_type }}' name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach
    
    <input type='{{ $form_type }}' name='base_date' id='input_base_date_of_form_to_show_daily'>
    
{{ Form::close() }}

<script>
    $('.date_button').on( 'click', function() {
        var date = $(this).data('date');
        var form = $('#form_to_show_daily');
        $('#input_base_date_of_form_to_show_daily').val( date );
        form.submit();
    });

</script>