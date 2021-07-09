@php


@endphp

<div class="col-12">&nbsp;</div>
@if( count( $tasks ))             
    <div class="card">
        <div class="card-header">
            <div class="row">
                <span class="col-5 btn btn_icon text-left" id="btn_toggle_task_item">@icon( caret-square-down )  未完のタスク</span>
                <span class="col-1 btn btn_icon ml-auto" onClick="location.reload();">@icon( sync ) </span>
            </div>
            <script>
                $('#btn_toggle_task_item').on( 'click', function() {
                     $('#task_list').toggle( 'blind', 100 );
                });
                
                
            </script>
            
        </div>
        <div class="container">
            <table class="card-body m-2 table table-border table-sm" id="task_list">
                <tr>
                    <th>タスク名</th>
                    <th>期日</th>
                </tr>
                
                @php
                $date = "";
                @endphp
                
                @foreach( $tasks as $task ) 
                    @php
                    $border_today = 0;
                    $style = $task->style();
                    @endphp
                
                    <tr class="show_modal_detail_object date_item" data-object_type='task' data-object_id={{ $task->id }}>
                        @php
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

                        <td style="">
                            <span>
                                @icon( check ) {{ $task->name }}
                            </span>
                        </td>
                        <td class="{{ $due_style }}">
                            {{ $due }}【{{ p_date_jp( $task->due_time->format('w') ) }}】
                        </td>
                        
                    </tr>
                @endforeach
                </div>
            </table>
        </div>
    </div>
@else
    <div class="col-12">未完のタスクはありません</div>
@endif

