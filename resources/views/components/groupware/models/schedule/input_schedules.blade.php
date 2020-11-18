<div>
    <div class='attached_schedules' id="schedule_ids_form">
        @foreach( $schedules as $i => $s )
            <div class="col-12">
                <div class='detach_schedule btn btn-sm btn-outline-secondary'>-</div>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route( 'groupware.schedule.show', [ 'schedule' => $s->id ] ) }}" target="_blank">詳細</a>
                <input type='hidden' name='schedules[]' value='{{ $s->id }}'>
                <pre class="text-trancated">【{{ $s->user->name }}】【{{ $s->print_start_time() }}】：{{ $s->name }}</pre>
            </div>
        @endforeach
    </div>
    
    <div class="btn btn-sm btn-outline-dark m-3" data-toggle="modal" data-target="#search_schedules_modal">スケジュール検索</div>
    <div class="modal fade" id="search_schedules_modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    スケジュール検索
                    @include( 'components.groupware.models.schedule.input_schedules_search' )
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>            
        </div>
    </div>
    
</div>

<script type="text/javascript">

    $(document).on('click', '.detach_schedule', function() {
        console.log( 'aaa' );
        $(this).parent().remove();
    });

    $(document).on( 'click', ".add_schedule_input", function() {
        var c = $(this).parent().clone(true).insertAfter( $(this).parent() );
        c.children( 'input' ).val('');
    });
    $(document).on( 'click', ".del_schedule_input", function() {
        var target = $(this).parent();
        if( target.parent().children().length > 1 ) {
            target.remove();
        }
    });
</script>


