<div>
    <div class='attached_file' id="file_ids_form">
        @foreach( $attached_files as $f )
                <div class='detach_file btn btn-sm btn-outline-secondary'>-</div>
                <input type='hidden' name='attached_files[]' value='{{ $f["id"] }}'>{{ $f['file_name'] }}</a>
                <a href="{{ route( 'groupware.file.show', [ 'file' => $f['id'] ] ) }}" target="_blank"><span class='search icon'></span></a>
                <br>
        @endforeach
    </div>
    
    <div class="btn btn-sm btn-outline-dark m-3" data-toggle="modal" data-target="#search_files_modal">ファイル検索</div>
    <div class="modal fade" id="search_files_modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    ファイル検索
                    
                    @include( 'components.groupware.models.file.input_files_search' )
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>            
        </div>
    </div>
    
    
    
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


