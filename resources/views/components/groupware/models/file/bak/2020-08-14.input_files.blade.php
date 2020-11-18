<div>
    @foreach( $attached_files as $f )
        <div class='attached_file'>
            <input type='hidden' name='attached_files[]' value='{{ $f["id"] }}'>{{ $f['file_name'] }}
            <div class='detach_file btn btn-sm btn-outline-secondary'>-</div>
        </div>
    @endforeach
    @if( count( $attached_files )) 
        <div class="col-12 m-2"></div>
    @endif
    <div>
        <input type="file" name="upload_files[]" class="input_upload_file">
        <div class='add_file_input btn btn-sm btn-outline-secondary'>+</div>
        <div class='del_file_input btn btn-sm btn-outline-secondary'>-</div>
    </div>
    <div class="col-12 m-3"></div>
</div>

<script type="text/javascript">

    $(document).on('click', '.detach_file', function() {
        console.log( 'aaa' );
        $(this).parent().remove();
    });

    $(document).on( 'click', ".add_file_input", function() {
        var c = $(this).parent().clone(true).insertAfter( $(this).parent() );
        c.children( 'input' ).val('');
    });
    $(document).on( 'click', ".del_file_input", function() {
        var target = $(this).parent();
        if( target.parent().children().length > 1 ) {
            target.remove();
        }
    });
</script>


