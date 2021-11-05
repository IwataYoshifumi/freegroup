@php

use App\myHttp\GroupWare\Models\Report;
use App\Http\Helpers\ScreenSize;

$schedule_id = [ 'schedule' => $schedule->id ];
$route_show       = route( 'groupware.schedule.show'  , $schedule_id ); 
$route_edit       = route( 'groupware.schedule.edit'  , $schedule_id ); 
$route_copy       = route( 'groupware.schedule.copy'  , $schedule_id );
$route_delete     = route( 'groupware.schedule.delete', $schedule_id ); 
$route_new_report = route( 'groupware.report.create'  , [ 'schedule_id' => $schedule_id] ); 
$show_more_info   = ( $route_name == 'groupware.schedule.show' or $route_name == 'groupware.schedule.show_modal' ) ? 1 : 0;

$target = ( $route_name == 'groupware.schedule.show_modal' ) ? "target='_parent'" : "";

@endphp

<div class="row m-1 w-100 container">
    


    @can( 'update', $schedule )
        <a class="btn btn_icon col-1 uitooltip" href="{{ $route_edit }}" title="変更"  {!! $target !!}>   {{-- htmlspecialchars OK --}}
            <i class="fas fa-pen"></i>
        </a>
    @endcan
    
    <a class="btn btn_icon col-1 uitooltip" href="{{ $route_copy }}" title="複製"  {!! $target !!}>   {{-- htmlspecialchars OK --}}
        <i class="fas fa-copy"></i>
    </a>
        
    @can( 'delete', $schedule )
        <a class="btn btn_icon col-1 uitooltip" href="{{ $route_delete }}" title="削除"  {!! $target !!}>   {{-- htmlspecialchars OK --}}
            <i class="fas fa-trash-alt"></i>
        </a>
    @endcan

    @if( $auth->can( 'update', $schedule ) and $auth->can( 'create', Report::class ))
        <a class="btn btn_icon col-1 uitooltip" href="{{ $route_new_report  }}" {!! $target !!} title="新規日報">@icon( clipboard )</a>   {{-- htmlspecialchars OK --}}
    @endif
    
    @if( $show_more_info )
        <div class="ml-auto" title="その他の情報">
            @if( $schedule->user_id == $auth->id )
                @if( $schedule->permission == "attendees" )
                    関連社員も予定修正可
                @elseif( $schedule->permission == "writers" )
                    カレンダー編集者も予定修正可
                @endif
            @endif
            <button class="btn btn_icon" id="schedule_info_btn">@icon( info-circle )</button>
        </div>
    @endif

    @if( ! ScreenSize::isMobile() )
        @if( $route_name == 'groupware.schedule.show_modal' )
            <a class="btn btn_icon uitooltip" href="{{ $route_show }}" {!! $target !!} title="全画面表示">@icon( expand )</a>   {{-- htmlspecialchars OK --}}
        @endif
    @endif
</div>

@if( $show_more_info )
    <div id="schedule_info_dialog" title="その他の情報">
    
        <div class="row">
            <div class="col-4 text-md-right">作成者</div>
            <div class="col-8">
                {{ $creator->p_dept_name() }} {{ $creator->name }} {{ $creator->grade }}
            </div>

            @if( $schedule->user_id != $schedule->updator_id )
                <div class="col-4 text-md-right">更新者</div>
                <div class="col-8">
                    {{ $updator->p_dept_name() }} {{ $updator->name }} {{ $updator->grade }} 更新
                </div>
            @endif

            <div class="col-4 text-md-right">作成日時</div>
            <div class="col-8">
                {{ $schedule->created_at }}
            </div>
            @if( $schedule->updated_at != $schedule->created_at )
                <div class="col-4 text-md-right">更新日時</div>
                <div class="col-8">
                    {{ $schedule->updated_at }}
                </div>
            @endif
    
            <div class="col-4 text-md-right">スケジュール変更権限</div>
            <div class="col-8">
                @if( $schedule->permission == "creator" )
                    予定作成者のみ変更可能
                @elseif( $schedule->permission == "attendees" )
                    関連社員も予定変更可能
                @elseif( $schedule->permission == "writers" )
                    関連社員・カレンダー編集者も予定変更可能
                @endif
                {{ $schedule->permission }}
            </div>

            <div class="col-4 text-md-right">閲覧範囲</div>
            <div class="col-8">
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
    