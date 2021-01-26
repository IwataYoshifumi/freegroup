@php

$schedule_id = [ 'schedule' => $schedule->id ];
$route_edit       = route( 'groupware.schedule.edit'  , $schedule_id ); 
$route_delete     = route( 'groupware.schedule.delete', $schedule_id ); 
$route_new_report = route( 'groupware.report.create'  , [ 'schedule_id' => $schedule_id] ); 
$show_more_info   = ( $route_name == 'groupware.schedule.show' ) ? 1 : 0;

@endphp

<div class="row m-1 w-100 container">

    @can( 'update', $schedule )
        <a class="btn col-1 m-1 uitooltip" style="font-size: 20px;" href="{{ $route_edit }}" title="変更">
            <i class="fas fa-pen"></i>
        </a>
    @endcan
        
    @can( 'delete', $schedule )
        <a class="btn col-1 m-1" style="font-size: 20px;" href="{{ $route_delete }}" title="削除">
            <i class="fas fa-trash-alt"></i>
        </a>
    @endcan

    @can( 'update', $schedule )
        <a class="btn btn-primary text-white col-2 col-lg-2 m-1" href="{{ $route_new_report  }}">
            <div class="d-block d-lg-none">新規日報</div>
            <div class="d-none d-lg-block">新規日報</div>
        </a>
    @endif
    
    @if( $show_more_info )
        <div class="ml-auto">
            @if( $schedule->user_id == $auth->id )
                @if( $schedule->permission == "attendees" )
                    関連社員も予定修正可
                @elseif( $schedule->permission == "writers" )
                    カレンダー編集者も予定修正可
                @endif
            @endif
            <button class="btn m-1" id="schedule_info_btn"><i class="fas fa-info-circle" style="font-size: 21px; color: black;"></i></button>
        </div>
    @endif
</div>

@if( $show_more_info )
    <div id="schedule_info_dialog" title="その他の情報">
    
        <div class="row">
            <div class="col-md-4 text-md-right">作成者</div>
            <div class="col-md-8">
                {{ $creator->p_dept_name() }} {{ $creator->name }} {{ $creator->grade }}
            </div>

            @if( $schedule->user_id != $schedule->updator_id )
                <div class="col-md-4 text-md-right">更新者</div>
                <div class="col-md-8">
                    {{ $updator->p_dept_name() }} {{ $updator->name }} {{ $updator->grade }} 更新
                </div>
            @endif

            <div class="col-md-4 text-md-right">作成日時</div>
            <div class="col-md-8">
                {{ $schedule->created_at }}
            </div>
            @if( $schedule->updated_at != $schedule->created_at )
                <div class="col-md-4 text-md-right">更新日時</div>
                <div class="col-md-8">
                    {{ $schedule->updated_at }}
                </div>
            @endif
    
            <div class="col-md-4 text-md-right">スケジュール変更権限</div>
            <div class="col-md-8">
                @if( $schedule->permission == "creator" )
                    予定作成者のみ変更可能
                @elseif( $schedule->permission == "attendees" )
                    関連社員も予定変更可能
                @elseif( $schedule->permission == "writers" )
                    関連社員・カレンダー編集者も予定変更可能
                @endif
                {{ $schedule->permission }}
            </div>

            <div class="col-md-4 text-md-right">閲覧範囲</div>
            <div class="col-md-8">
                @if( $calendar->type == 'public' )
                    公開
                @elseif( $calendar->type == 'private' )
                    閲覧制限あり
                @elseif( $calendar->type == 'campany-wide' )
                    全社公開
                @endif
                {{ $calendar->type }}
            </div>
            
                        
        
            
        </div>
    </div>
    
    <script>
        $( function() {
            $('#schedule_info_dialog').dialog({
                autoOpen: false,
                height: 'auto',
                width: 600,
                show: { effect: 'blind',   duration: 10 },
                hide: { effect: 'explode', duration: 10 }
            }); 
            $('#schedule_info_btn').on( 'click', function() {
                $('#schedule_info_dialog').dialog('open'); 
            });
        });    
        
        
    </script>
@endif
    