@php


@endphp

<div class="col-12">&nbsp;</div>
@if( count( $tasks ))             
    <div class="card">
        <div class="card-header">
            <div class="row">
                <span class="col   btn btn_icon text-left text-truncate" id="btn_toggle_task_item">@icon( caret-square-down ) 直近１年のタスク</span>
                <span class="col-1 btn btn_icon ml-auto mr-2" onClick="location.reload();">@icon( sync ) </span>
            </div>
            <script>
                $('#btn_toggle_task_item').on( 'click', function() {
                     $('#task_list').toggle( 'blind', 100 );
                });
            </script>
        </div>
        <div class="container">
            <div id="task_list" class="table m-2">
                <div class="row">
                    <div class="d-none d-lg-block col-6 text-left text-truncate font-weight-bold">タスク名</div>
                    <div class="d-none d-lg-block col-6 text-left text-truncate font-weight-bold">期日</div>
                    <hr  class="d-none d-lg-block col-12">
                    
                    @php
                    $date = "";
                    @endphp
                    
                    @foreach( $tasks as $task ) 
                        @php
                        $border_today = 0;
                        $style = $task->style();

                        $extremly_due_exceed = "";
                        $due = $task->due_time->format( 'n月j日' );

                        if( $task->due_time->diffInDays( $today ) == 0 ) {
                            #$due_style = "text-primary font-weight-bold";
                            
                        } elseif( $task->due_time->lt( $today )) {
                            #$due_style = "text-danger";
                            
                            if( $task->due_time->diffInDays( $today ) >= 360 ) {
                                $due = $task->due_time->format( 'Y年n月j日' );
                                
                            } elseif( $task->due_time->diffInDays( $today ) >= 30 ) {
                                #$due .= " !";
                            }
                            
                        } else {
                            $due_style = "";
                        }
                        $task_style = ( $task->status == "完了" ) ? "text-decoration:line-through" : "";
                        
                        @endphp
                        <div class="col-6 btn text-left text-truncate object_to_show_detail" data-object='task' data-object_id={{ $task->id }} style="{{ $task_style }}">
                            @if( $task->status == "完了" )
                                @icon( check-circle )
                            @else
                                @icon( check-circle-r )
                            @endif
                             {{ $task->name }}
                        </div>
                        <div class="col-6 btn text-left text-truncate object_to_show_detail" data-object='task' data-object_id={{ $task->id }}>
                            {{ $due }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@else
    <div class="col-12">未完のタスクはありません</div>
@endif

