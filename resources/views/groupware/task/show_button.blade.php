@php

$task_id = [ 'task' => $task->id ];

$route_show       = route( 'groupware.task.show'  , $task_id ); 
$route_edit       = route( 'groupware.task.edit'  , $task_id ); 
$route_copy       = route( 'groupware.task.copy'  , $task_id ); 
$route_delete     = route( 'groupware.task.delete', $task_id ); 
$route_new_task = route( 'groupware.task.create'  , $task_id ); 
$show_more_info   = ( $route_name == 'groupware.task.show' ) ? 1 : 0;

$target = ( $route_name == 'groupware.task.show_modal' ) ? "target='_parent'" : "";

@endphp

<div class="row m-1 w-100 container">

    @can( 'update', $task )
        <a class="btn icon_btn uitooltip" href="{{ $route_edit }}" title="変更"  {!! $target !!}> {{-- htmlspecialchars OK --}}
            @icon( edit )
        </a>
    @endcan
    
    <a class="btn btn_icon uitooltip" href="{{ $route_copy }}" title="複製"  {!! $target !!}> {{-- htmlspecialchars OK --}}
        <i class="fas fa-copy"></i>
    </a>
        
    @can( 'delete', $task )
        <a class="btn btn_icon uitooltip" href="{{ $route_delete }}" title="削除"  {!! $target !!}> {{-- htmlspecialchars OK --}}
            @icon( trash-alt )
        </a>
    @endcan

    <div class="ml-auto">
    @if( $show_more_info )
        @if( $tasklist->is_disabled() )
             <span class="alert-danger p-2 uitooltip" title="タスクリストが無効化中の為、編集・削除はできません">タスクリスト管理者設定により無効化中</span>   
        @else
            @if( $task->user_id == $auth->id )
                @if( $task->permission == "attendees" )
                    関連社員も予定修正可
                @elseif( $task->permission == "writers" )
                    タスクリスト編集者も予定修正可
                @endif
            @endif
        @endif
        <button class="btn m-1" id="task_info_btn"><i class="fas fa-info-circle" style="font-size: 21px; color: black;"></i></button>
    @endif

    @if( $route_name == 'groupware.task.show_modal' )
        <a class="btn btn_icon uitooltip" href="{{ $route_show }}" {!! $target !!} title="全画面表示">@icon( expand )</a>   {{-- htmlspecialchars OK --}}
    @endif
    </div>

</div>

@if( $show_more_info )
    <div id="task_info_dialog" title="その他の情報">
    
        <div class="row">
            <div class="col-md-4 text-md-right">作成者</div>
            <div class="col-md-8">
                {{ $creator->p_dept_name() }} {{ $creator->name }} {{ $creator->grade }}
            </div>

            @if( $task->user_id != $task->updator_id )
                <div class="col-md-4 text-md-right">更新者</div>
                <div class="col-md-8">
                    {{ $updator->p_dept_name() }} {{ $updator->name }} {{ $updator->grade }} 更新
                </div>
            @endif

            <div class="col-md-4 text-md-right">作成日時</div>
            <div class="col-md-8">
                {{ $task->created_at }}
            </div>
            @if( $task->updated_at != $task->created_at )
                <div class="col-md-4 text-md-right">更新日時</div>
                <div class="col-md-8">
                    {{ $task->updated_at }}
                </div>
            @endif
    
            <div class="col-md-4 text-md-right">タスク変更権限</div>
            <div class="col-md-8">
                @if( $task->permission == "creator" )
                    予定作成者のみ変更可能
                @elseif( $task->permission == "attendees" )
                    関連社員も予定変更可能
                @elseif( $task->permission == "writers" )
                    関連社員・タスクリスト編集者も予定変更可能
                @endif
                {{ $task->permission }}
            </div>

            <div class="col-md-4 text-md-right">閲覧範囲</div>
            <div class="col-md-8">
                @if( $tasklist->type == 'public' )
                    公開
                @elseif( $tasklist->type == 'private' )
                    閲覧制限あり
                @elseif( $tasklist->type == 'campany-wide' )
                    全社公開
                @endif
                {{ $tasklist->type }}
            </div>
            
                        
        
            
        </div>
    </div>
    
    <script>
        $( function() {
            $('#task_info_dialog').dialog({
                autoOpen: false,
                height: 'auto',
                width: 600,
                show: { effect: 'blind',   duration: 10 },
                hide: { effect: 'explode', duration: 10 }
            }); 
            $('#task_info_btn').on( 'click', function() {
                $('#task_info_dialog').dialog('open'); 
            });
        });    
        
        
    </script>
@endif
    