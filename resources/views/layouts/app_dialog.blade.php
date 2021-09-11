<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include( 'layouts.header' )
@include( 'layouts.groupware.app_dialog' )

<!--
  --
  --　待ち時間ローディングアニメーション
  --
  -->
<div id="loading">@icon( loading )</div>

<script>
    /*
     *
     * JQuery UIのツールチップ
     *
     */
    $(document).ready( function() { 
        $('.tabs').tabs();
        $('.menu').menu();
        $('.dialog').dialog();
        $('.selectmenu').selectmenu();
        $('.uitooltip' ).uitooltip();
        $('.checkboxradio').checkboxradio( { icon: false} ); 
        $('.controlgroup' ).controlgroup(); 
        $('.acordion' ).accordion(); 
        $('.sortable' ).sortable(); 
        $('.selectable').selectable();
        $('.draggable').draggable();
    });
    
    $('#loading').hide();

</script>
</html>
