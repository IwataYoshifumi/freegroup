
{{ Form::select( $form_name, [], old( $form_name ),  [ 'class' => $form_class, 'id' => $form_id ] ) }}

@if( ! is_null( $dept_form_id ))
    <script>
        $('#{{ $dept_form_id}}').change( function() {
            var dept_id = $(this).val();
            var search = '';
            console.log( dept_id );
            
            var url    = "{{ route( 'user.json.search' ) }}";

            if( dept_id ) { 
                $.ajax( url, {
                    ttype: 'get',
                    data:  { name : search, dept_id : dept_id },
                    dataType: 'json',
                }).done( function( data ) {
                    // console.log( data );
                    var user_select = $('#{{ $form_id }}');
                    $(user_select).children().remove();
                    $(user_select).append($("<option>").val("").text(""));
                    $.each( data, function( i, d ) {
                        // console.log( i, d['name'] );
                        $(user_select).append($("<option>").val(d['id']).text(d['name'])); 
                    });
                });
            }
        });
        
        $('.document').ready( function() {
            $('#{{ $dept_form_id}}').change();
        });
        
    </script>
@endif

