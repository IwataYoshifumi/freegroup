@push( 'javascript' )

    <script type="text/javascript">
        function sample() {
            backgroud_color = $('#color').val();
            text_color = $('#text-color').val();
            console.log( backgroud_color, text_color );
            console.log( $('#sample1').css( 'background-color'));
            $('#sample1').css( 'background-color', backgroud_color );
            $('#sample1').css( 'color', text_color );
        }
    
        $(document).ready(function(){
            sample();
            $('.permission_radio').checkboxradio();
            $('#google_sync_on').checkboxradio();

        });                                
        
    </script>    
@endpush