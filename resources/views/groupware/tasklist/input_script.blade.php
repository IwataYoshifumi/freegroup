@push( 'javascript' )
<script>

    //　タイプを変更したら、入力フォームを切り替える
    //
    $('.select_types').change( function() {
        change_select_type_to_toggle_form_show_hide( $(this));
    });
    
    //  タイプの入力値で、入力フォームを切り替える
    //
    function change_select_type_to_toggle_form_show_hide( obj ) {
        var type = $(obj).val();
        var i = $(obj).data('id');
        // console.log( type, i );
        
        $('.list_type_'+ i ).each( function() {
            // console.log( $(this).data('type'), type);
            if( $(this).data('type') == type ) {
                $(this).show();
            } else {
                $(this).hide();
                // $(this).show();
            }
        });
    }


    //　フォーム表示時の初期動作
    //
    $('.document').ready( function() {
        console.log( 'init' );
        init_toggle_form_show_hide();
    });

    //　フォーム表示時の初期動作（タイプ値でフォーム表示を切り替える）
    //
    function init_toggle_form_show_hide() {
        $('.select_types').each( function() {
            $(this).change();
        });
    }

    //　テーブルをソータブルに設定
    //
    $('#sortdata').sortable();
    
    // order の値をを変更
    $('#sortdata').bind('sortstop',function(){
        $(this).find('[name="num_data"]').each(function(idx){
            $(this).html(idx+1);
            
        });
        //$('.order_input').each( function(idx) {
        //    $(this).val( idx+1 );
        //});
    });

</script>
@endpush