@php

#if_debug( $tasks );

@endphp

<div class="col-12">&nbsp;</div>
@if( count( $tasks ))             
    <div class="card">
        <div class="card-header p-1 p-lg-20">
            <div class="row">
                <span class="col   btn btn_icon text-left" id="btn_toggle_task_item">@icon( caret-square-down ) 本日のタスク</span>
                <span class="col-1 btn btn_icon ml-auto d-none d-lg-block" onClick="location.reload();">@icon( sync ) </span>
            </div>
            <script>
                $('#btn_toggle_task_item').on( 'click', function() {
                     $('#task_list').toggle( 'blind', 100 );
                });
            </script>
            
        </div>
        <div class="m-1" id="task_list">
            <div class="row no-gutters">
                <div class="col-7 d-none d-lg-block font-weight-bold">タスク名</div>
                <div class="col-4 d-none d-lg-block font-weight-bold">期限</div>
                <hr  class="col-12 d-none d-lg-block m-0 mb-1">
                
                @php
                $date = "";
                @endphp
                
                @foreach( $tasks as $task ) 
                    @php
                    $style = $task->style();
                    
                    $extremly_due_exceed = "";
                    $due = $task->due_time->format( 'n月j日' );

                    if( $task->due_time->diffInDays( $today ) == 0 ) {
                        $due_style = "text-primary font-weight-bold";
                        
                    } elseif( $task->due_time->lt( $today )) {
                        $due_style = "text-danger";
                        
                        if( $task->due_time->diffInDays( $today ) >= 360 ) {
                            $due = $task->due_time->format( 'Y年n月j日' );
                            
                        } elseif( $task->due_time->diffInDays( $today ) >= 30 ) {
                            #$due .= " !";
                        }
                        
                    } else {
                        $due_style = "";
                    }
                    
                    
                    @endphp
                    <div class="object_to_show_detail date_item text-truncate col-7" data-object='task' data-object_id={{ $task->id }}>
                            @icon( check ) {{ $task->name }}
                    </div>
                    <div class="object_to_show_detail date_item text-truncate col-4 {{ $due_style }}" data-object='task' data-object_id={{ $task->id }}>
                        {{ $due }}【{{ p_date_jp( $task->due_time->format('w') ) }}】
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@else
    <div class="col-12">本日のタスクはありません</div>
@endif

